<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use App\Helpers\NumberHelper;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Helper privat untuk menghasilkan file Microsoft Excel (.xlsx) murni dengan styling profesional
     */
    private function streamXlsxResponse(string $title, array $headers, array $dataRows, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title, 0, 31));

        // Tulis baris Header
        $sheet->fromArray([$headers], null, 'A1');

        $highestColumn = $sheet->getHighestColumn();
        $totalRows = count($dataRows) + 1;

        // Styling Baris Header (Navy/Slate 900 dengan teks putih tebal)
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Tulis baris Data
        if (!empty($dataRows)) {
            $sheet->fromArray($dataRows, null, 'A2');

            // Border halus untuk semua data
            $sheet->getStyle('A2:' . $highestColumn . $totalRows)->applyFromArray([
                'font' => [
                    'size' => 10,
                    'name' => 'Calibri',
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Auto-fit lebar setiap kolom
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Pragma', 'public');

        return $response;
    }

    /**
     * Redirect master laporan route ke tab laporan pertama (Presensi).
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('reports.attendances');
    }

    /**
     * Laporan 1: Log & Rekapitulasi Presensi Karyawan
     */
    public function attendances(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $userId = $request->input('user_id');
        $status = $request->input('status');
        $isLate = $request->input('is_late');

        $query = Attendance::with('user');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($isLate !== null && $isLate !== '') {
            $query->where('is_late', (bool) $isLate);
        }

        // Ambil data untuk ringkasan statistik (semua baris yang sesuai filter)
        $statsData = (clone $query)->get();
        $stats = [
            'total' => $statsData->count(),
            'on_site' => $statsData->where('status', 'HADIR_ON_SITE')->count(),
            'wfh' => $statsData->where('status', 'WFH')->count(),
            'sakit_izin' => $statsData->whereIn('status', ['SAKIT', 'IZIN'])->count(),
            'late' => $statsData->where('is_late', true)->count(),
            'total_overtime' => $statsData->sum('overtime_hours'),
        ];

        // Pagination untuk tabel data
        $attendances = $query->orderBy('date', 'desc')->orderBy('time_in', 'desc')->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('reports.attendances', compact(
            'attendances',
            'stats',
            'users',
            'startDate',
            'endDate',
            'userId',
            'status',
            'isLate'
        ));
    }

    /**
     * Export Excel/CSV untuk Laporan Presensi
     */
    public function exportAttendances(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $userId = $request->input('user_id');
        $status = $request->input('status');
        $isLate = $request->input('is_late');

        $query = Attendance::with('user');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($isLate !== null && $isLate !== '') {
            $query->where('is_late', (bool) $isLate);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();

        $filename = "Laporan_Presensi_ChronoHR_{$startDate}_sd_{$endDate}.xlsx";

        $headers = [
            'No',
            'Nama Karyawan',
            'Email',
            'Ikatan Kerja',
            'Tanggal Presensi',
            'Jam Masuk (WIB)',
            'Jam Pulang (WIB)',
            'Status Kehadiran',
            'Metode Check-In',
            'Ketepatan Waktu',
            'Menit Terlambat',
            'Lembur (Jam)',
            'Koordinat GPS',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($attendances as $att) {
            $coord = ($att->latitude && $att->longitude)
                ? "{$att->latitude}, {$att->longitude}"
                : '-';

            $dataRows[] = [
                $no++,
                $att->user->name ?? 'User Terhapus',
                $att->user->email ?? '-',
                $att->user->employment_type ?? '-',
                $att->date?->format('Y-m-d') ?? (string) $att->date,
                $att->time_in ? substr($att->time_in, 0, 5) : '-',
                $att->time_out ? substr($att->time_out, 0, 5) : '-',
                $att->status,
                $att->method,
                $att->is_late ? 'Terlambat' : 'Tepat Waktu',
                $att->late_minutes ?? 0,
                $att->overtime_hours ?? 0,
                $coord,
            ];
        }

        return $this->streamXlsxResponse('Laporan Presensi', $headers, $dataRows, $filename);
    }

    /**
     * Laporan 2: Rekapitulasi Slip Gaji & Beban Payroll (Filter status slip ditiadakan)
     */
    public function payrolls(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $userId = $request->input('user_id');
        $employmentType = $request->input('employment_type');

        $query = Payroll::with('user')
            ->where('month', $month)
            ->where('year', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($employmentType) {
            $query->whereHas('user', function ($q) use ($employmentType) {
                $q->where('employment_type', $employmentType);
            });
        }

        // Ambil data untuk ringkasan statistik (semua baris yang sesuai filter)
        $statsData = (clone $query)->get();
        $stats = [
            'total_count' => $statsData->count(),
            'total_net' => (float) $statsData->sum('net_salary'),
            'total_gross' => (float) $statsData->sum('gross_salary'),
            'total_wfh_deduction' => (float) $statsData->sum('wfh_deduction_amount'),
            'total_leave_deduction' => (float) $statsData->sum('leave_deduction_amount'),
            'total_overtime_pay' => (float) $statsData->sum('overtime_pay'),
            'published_count' => $statsData->where('status', 'PUBLISHED')->count(),
            'draft_count' => $statsData->where('status', 'DRAFT')->count(),
        ];

        // Pagination untuk tabel data
        $payrolls = $query->orderBy('net_salary', 'desc')->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('reports.payrolls', compact(
            'payrolls',
            'stats',
            'users',
            'month',
            'year',
            'userId',
            'employmentType'
        ));
    }

    /**
     * Export Excel/CSV untuk Laporan Slip Gaji
     */
    public function exportPayrolls(Request $request): StreamedResponse
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $userId = $request->input('user_id');
        $employmentType = $request->input('employment_type');

        $query = Payroll::with('user')
            ->where('month', $month)
            ->where('year', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($employmentType) {
            $query->whereHas('user', function ($q) use ($employmentType) {
                $q->where('employment_type', $employmentType);
            });
        }

        $payrolls = $query->orderBy('net_salary', 'desc')->get();

        $filename = "Laporan_Slip_Gaji_ChronoHR_Periode_{$month}_{$year}.xlsx";

        $headers = [
            'No',
            'Nama Karyawan',
            'Email',
            'Ikatan Kerja',
            'Periode Bulan',
            'Periode Tahun',
            'Hari Kerja Efektif',
            'Hadir On-Site (Hari)',
            'WFH (Hari)',
            'Sakit (Hari)',
            'Izin (Hari)',
            'Lembur (Jam)',
            'Gaji Pokok (Rp)',
            'Tarif Harian (Rp)',
            'Potongan WFH (Rp)',
            'Potongan Sakit/Izin (Rp)',
            'Uang Lembur (Rp)',
            'Gaji Bersih / Netto (Rp)',
            'Status Slip',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($payrolls as $p) {
            $dataRows[] = [
                $no++,
                $p->user->name ?? 'User Terhapus',
                $p->user->email ?? '-',
                $p->user->employment_type ?? '-',
                $p->month,
                $p->year,
                $p->total_working_days,
                $p->total_present_onsite,
                $p->total_wfh_days,
                $p->total_sick_days ?? 0,
                $p->total_permit_days ?? 0,
                $p->total_overtime_hours,
                $p->user->basic_salary ?? 0,
                $p->daily_salary_rate,
                $p->wfh_deduction_amount,
                $p->leave_deduction_amount ?? 0,
                $p->overtime_pay,
                $p->net_salary,
                $p->status,
            ];
        }

        return $this->streamXlsxResponse('Rekapitulasi Slip Gaji', $headers, $dataRows, $filename);
    }

    /**
     * Unduh / Cetak Slip Gaji Personil dalam format PDF resmi
     */
    public function downloadSlipPdf(Payroll $payroll, Request $request): Response
    {
        $payroll->load(['user']);

        $user = $payroll->user;
        $monthName = Carbon::create($payroll->year, $payroll->month, 1)->translatedFormat('F');
        $terbilang = NumberHelper::terbilang($payroll->net_salary);

        $officeSettings = [
            'name' => Setting::getValue('office_name', 'PT ChronoHR Teknologi Indonesia'),
            'address' => Setting::getValue('office_address', 'Gedung Pusat ChronoHR, Jakarta'),
            'payday' => Setting::getValue('payday_date', 25),
        ];

        $pdf = Pdf::loadView('reports.slip_pdf', compact(
            'payroll',
            'user',
            'monthName',
            'terbilang',
            'officeSettings'
        ))->setPaper('a4', 'portrait');

        $cleanUserName = preg_replace('/[^A-Za-z0-9_-]/', '_', $user->name ?? 'Karyawan');
        $filename = "Slip_Gaji_ChronoHR_{$cleanUserName}_{$monthName}_{$payroll->year}.pdf";

        if ($request->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Laporan 3: Rekapitulasi Pengajuan Cuti, Izin & WFH
     */
    public function leaves(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $userId = $request->input('user_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = LeaveRequest::with(['user', 'approver']);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }

        // Ambil data untuk ringkasan statistik (semua baris yang sesuai filter)
        $statsData = (clone $query)->get();
        $stats = [
            'total' => $statsData->count(),
            'approved' => $statsData->where('status', 'APPROVED')->count(),
            'pending' => $statsData->where('status', 'PENDING')->count(),
            'rejected' => $statsData->where('status', 'REJECTED')->count(),
            'wfh_count' => $statsData->where('type', 'WFH')->count(),
            'sakit_count' => $statsData->where('type', 'SAKIT')->count(),
            'izin_count' => $statsData->where('type', 'IZIN')->count(),
        ];

        // Pagination untuk tabel data
        $leaves = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('reports.leaves', compact(
            'leaves',
            'stats',
            'users',
            'startDate',
            'endDate',
            'userId',
            'type',
            'status'
        ));
    }

    /**
     * Export Excel/CSV untuk Laporan Pengajuan
     */
    public function exportLeaves(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $userId = $request->input('user_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = LeaveRequest::with(['user', 'approver']);

        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $leaves = $query->orderBy('start_date', 'desc')->get();

        $filename = "Laporan_Pengajuan_ChronoHR_{$startDate}_sd_{$endDate}.xlsx";

        $headers = [
            'No',
            'Nama Karyawan',
            'Email',
            'Ikatan Kerja',
            'Jenis Pengajuan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Hari)',
            'Alasan / Keperluan',
            'Status Keputusan',
            'Ditinjau Oleh',
            'Waktu Ditinjau',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($leaves as $lv) {
            $days = Carbon::parse($lv->start_date)->diffInDays(Carbon::parse($lv->end_date)) + 1;

            $dataRows[] = [
                $no++,
                $lv->user->name ?? 'User Terhapus',
                $lv->user->email ?? '-',
                $lv->user->employment_type ?? '-',
                $lv->type,
                $lv->start_date,
                $lv->end_date,
                $days,
                $lv->reason,
                $lv->status,
                $lv->approver->name ?? 'Belum Ditinjau',
                $lv->status !== 'PENDING' && $lv->updated_at ? $lv->updated_at->format('Y-m-d H:i:s') : '-',
            ];
        }

        return $this->streamXlsxResponse('Laporan Pengajuan', $headers, $dataRows, $filename);
    }

    /**
     * Laporan 4: Monitoring Laporan Task Kerja Harian (Morning Plan & Evening Review)
     */
    public function dailyTasks(Request $request): View
    {
        $date = $request->input('date', now()->toDateString());
        $statusFilter = $request->input('status');
        $search = $request->input('search');
        $userId = $request->input('user_id');

        $usersQuery = User::with(['dailyReports' => function ($q) use ($date) {
            $q->whereDate('date', $date);
        }])->orderBy('name');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($userId) {
            $usersQuery->where('id', $userId);
        }

        // Ambil data untuk ringkasan KPI statistik (seluruh personil)
        $allUsers = (clone $usersQuery)->get();
        $totalUsers = $allUsers->count();
        $morningCount = 0;
        $completedCount = 0;
        $morningOnlyCount = 0;
        $notSubmittedCount = 0;

        foreach ($allUsers as $u) {
            $rep = $u->dailyReports->first();
            if (!$rep) {
                $notSubmittedCount++;
            } elseif ($rep->status === 'COMPLETED') {
                $morningCount++;
                $completedCount++;
            } else {
                $morningCount++;
                $morningOnlyCount++;
            }
        }

        $stats = [
            'total' => $totalUsers,
            'morning_submitted' => $morningCount,
            'completed' => $completedCount,
            'morning_only' => $morningOnlyCount,
            'not_submitted' => $notSubmittedCount,
        ];

        // Filter berdasarkan status jika dipilih
        if ($statusFilter === 'COMPLETED') {
            $usersQuery->whereHas('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date)->where('status', 'COMPLETED');
            });
        } elseif ($statusFilter === 'MORNING_ONLY') {
            $usersQuery->whereHas('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date)->where('status', 'MORNING_ONLY');
            });
        } elseif ($statusFilter === 'NOT_SUBMITTED') {
            $usersQuery->whereDoesntHave('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        $users = $usersQuery->paginate(15)->withQueryString();
        $allEmployees = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('reports.daily_tasks', compact(
            'users',
            'stats',
            'allEmployees',
            'date',
            'statusFilter',
            'search',
            'userId'
        ));
    }

    /**
     * Export Excel/CSV untuk Monitoring Task Harian
     */
    public function exportDailyTasks(Request $request): StreamedResponse
    {
        $date = $request->input('date', now()->toDateString());
        $statusFilter = $request->input('status');
        $search = $request->input('search');
        $userId = $request->input('user_id');

        $usersQuery = User::with(['dailyReports' => function ($q) use ($date) {
            $q->whereDate('date', $date);
        }])->orderBy('name');

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($userId) {
            $usersQuery->where('id', $userId);
        }

        if ($statusFilter === 'COMPLETED') {
            $usersQuery->whereHas('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date)->where('status', 'COMPLETED');
            });
        } elseif ($statusFilter === 'MORNING_ONLY') {
            $usersQuery->whereHas('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date)->where('status', 'MORNING_ONLY');
            });
        } elseif ($statusFilter === 'NOT_SUBMITTED') {
            $usersQuery->whereDoesntHave('dailyReports', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });
        }

        $users = $usersQuery->get();

        $filename = "Monitoring_Task_Harian_ChronoHR_{$date}.xlsx";

        $headers = [
            'No',
            'Tanggal Laporan',
            'Nama Karyawan',
            'Email',
            'Ikatan Kerja',
            'Status Laporan',
            'Jam Submit Pagi',
            'Rencana Task Pagi (Check-In Plan)',
            'Jam Submit Sore',
            'Task Selesai (Completed)',
            'Task Belum Selesai (Pending)',
            'Task Tambahan (Ad-Hoc)',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($users as $u) {
            $rep = $u->dailyReports->first();

            $statusLabel = 'Belum Lapor Task';
            if ($rep) {
                $statusLabel = ($rep->status === 'COMPLETED')
                    ? 'Lengkap (Pagi & Sore)'
                    : 'Menunggu Laporan Sore (Hanya Pagi)';
            }

            $dataRows[] = [
                $no++,
                $date,
                $u->name,
                $u->email,
                $u->employment_type ?? '-',
                $statusLabel,
                $rep?->morning_submitted_at ? substr($rep->morning_submitted_at, 0, 5) : '-',
                $rep?->morning_planned_tasks ?? '-',
                $rep?->evening_submitted_at ? substr($rep->evening_submitted_at, 0, 5) : '-',
                $rep?->evening_completed_tasks ?? '-',
                $rep?->evening_pending_tasks ?? '-',
                $rep?->evening_additional_tasks ?? '-',
            ];
        }

        return $this->streamXlsxResponse('Monitoring Task Harian', $headers, $dataRows, $filename);
    }

    /**
     * Simpan / Buat Laporan Task Kerja Harian
     */
    public function storeDailyTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'morning_planned_tasks' => 'required|string',
            'evening_completed_tasks' => 'nullable|string',
            'evening_pending_tasks' => 'nullable|string',
            'evening_additional_tasks' => 'nullable|string',
        ]);

        $allTasks = $request->input('all_morning_tasks');
        $completedIndices = (array) $request->input('completed_task_indices', []);
        $additionalTasks = trim((string) ($request->input('evening_additional_tasks') ?? $validated['evening_additional_tasks'] ?? ''));

        if (is_array($allTasks) && count($allTasks) > 0 && ($request->input('with_evening_checklist') == '1' || $request->input('with_evening_checklist') === true)) {
            $completedList = [];
            $pendingList = [];

            foreach ($allTasks as $idx => $taskText) {
                $taskClean = trim($taskText);
                if (in_array((string) $idx, $completedIndices, true) || in_array((int) $idx, $completedIndices, true)) {
                    $completedList[] = $taskClean;
                } else {
                    $pendingList[] = $taskClean;
                }
            }

            // Juga proses jika ada item tambahan checklist
            $additionalTasksChecklist = (array) $request->input('additional_tasks_checklist', []);
            $completedAdditionalIndices = (array) $request->input('completed_additional_indices', []);
            foreach ($additionalTasksChecklist as $aIdx => $aTaskText) {
                $aTaskClean = trim($aTaskText);
                if (in_array((string) $aIdx, $completedAdditionalIndices, true) || in_array((int) $aIdx, $completedAdditionalIndices, true)) {
                    $completedList[] = "[Tambahan] " . $aTaskClean;
                } else {
                    $pendingList[] = "[Tambahan] " . $aTaskClean;
                }
            }

            $completedTasks = !empty($completedList) ? implode("\n", $completedList) : 'Tidak ada task yang diselesaikan';
            $pendingTasks = !empty($pendingList) ? implode("\n", $pendingList) : '-';
            $isEveningFilled = true;
        } else {
            $completedTasks = $validated['evening_completed_tasks'] ?? null;
            $pendingTasks = $validated['evening_pending_tasks'] ?? null;
            $isEveningFilled = !empty(trim($completedTasks ?? ''));
        }

        DailyReport::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
            ],
            [
                'morning_submitted_at' => now()->format('H:i:s'),
                'morning_planned_tasks' => $validated['morning_planned_tasks'],
                'evening_submitted_at' => $isEveningFilled ? now()->format('H:i:s') : null,
                'evening_completed_tasks' => $completedTasks,
                'evening_pending_tasks' => $pendingTasks,
                'evening_additional_tasks' => !empty($additionalTasks) ? $additionalTasks : null,
                'status' => $isEveningFilled ? 'COMPLETED' : 'MORNING_ONLY',
            ]
        );

        return back()->with('success', 'Laporan task kerja harian berhasil dicatat.');
    }

    /**
     * Perbarui Laporan Progres Sore (Check-out Review berbasis Checklist)
     */
    public function updateDailyTask(Request $request, DailyReport $dailyReport): RedirectResponse
    {
        $allTasks = $request->input('all_morning_tasks');
        $completedIndices = (array) $request->input('completed_task_indices', []);
        $additionalTasks = trim((string) $request->input('evening_additional_tasks', ''));

        if (is_array($allTasks) && count($allTasks) > 0) {
            $completedList = [];
            $pendingList = [];

            foreach ($allTasks as $idx => $taskText) {
                $taskClean = trim($taskText);
                if (in_array((string) $idx, $completedIndices, true) || in_array((int) $idx, $completedIndices, true)) {
                    $completedList[] = $taskClean;
                } else {
                    $pendingList[] = $taskClean;
                }
            }

            // Juga proses jika ada item tambahan checklist
            $additionalTasksChecklist = (array) $request->input('additional_tasks_checklist', []);
            $completedAdditionalIndices = (array) $request->input('completed_additional_indices', []);
            foreach ($additionalTasksChecklist as $aIdx => $aTaskText) {
                $aTaskClean = trim($aTaskText);
                if (in_array((string) $aIdx, $completedAdditionalIndices, true) || in_array((int) $aIdx, $completedAdditionalIndices, true)) {
                    $completedList[] = "[Tambahan] " . $aTaskClean;
                } else {
                    $pendingList[] = "[Tambahan] " . $aTaskClean;
                }
            }

            $completedTasks = !empty($completedList) ? implode("\n", $completedList) : 'Tidak ada task yang diselesaikan';
            $pendingTasks = !empty($pendingList) ? implode("\n", $pendingList) : '-';
        } else {
            $validated = $request->validate([
                'evening_completed_tasks' => 'required|string',
                'evening_pending_tasks' => 'nullable|string',
                'evening_additional_tasks' => 'nullable|string',
            ]);

            $completedTasks = $validated['evening_completed_tasks'];
            $pendingTasks = $validated['evening_pending_tasks'] ?? '-';
            $additionalTasks = $validated['evening_additional_tasks'] ?? '';
        }

        $dailyReport->update([
            'evening_submitted_at' => now()->format('H:i:s'),
            'evening_completed_tasks' => $completedTasks,
            'evening_pending_tasks' => $pendingTasks,
            'evening_additional_tasks' => !empty($additionalTasks) ? $additionalTasks : null,
            'status' => 'COMPLETED',
        ]);

        return back()->with('success', 'Laporan progres kerja sore berhasil diperbarui dengan checklist task.');
    }
}
