<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeSanction;
use App\Models\SanctionRule;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Database\Seeders\SanctionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SanctionController extends Controller
{
    public function index(Request $request): View
    {
        if (SanctionRule::count() === 0) {
            (new SanctionSeeder())->run();
        }

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $userId = $request->input('user_id');
        $level = $request->input('level');
        $status = $request->input('status');
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $query = EmployeeSanction::with(['user', 'rule', 'issuer'])
            ->where('month', $month)
            ->where('year', $year)
            ->orderBy('incident_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($level && in_array($level, ['TEGURAN_LISAN', 'SP1', 'SP2', 'SP3'])) {
            $query->where('level', $level);
        }

        if ($status && in_array($status, ['ACTIVE', 'RESOLVED', 'REVOKED'])) {
            $query->where('status', $status);
        }

        $allPeriodSanctions = (clone $query)->get();
        $activeSanctions = $allPeriodSanctions->where('status', 'ACTIVE');

        $stats = [
            'total_active' => $activeSanctions->count(),
            'teguran_count' => $activeSanctions->where('level', 'TEGURAN_LISAN')->count(),
            'sp_count' => $activeSanctions->whereIn('level', ['SP1', 'SP2', 'SP3'])->count(),
            'auto_detected_count' => $activeSanctions->where('source', 'AUTO_DETECTED')->count(),
            'total_deduction' => (float) $activeSanctions->sum('deduction_amount'),
        ];

        $sanctions = $query->paginate($perPage)->withQueryString();
        $rules = SanctionRule::orderBy('category')->orderBy('name')->get();
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('is_attendance_required', false);
        })->orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        // Rekap kandidat pelanggaran otomatis (Terlambat / Alpa melebihi ambang batas aturan aktif)
        $autoCandidates = $this->findAutoViolationCandidates($month, $year);

        return view('sanctions.index', compact(
            'sanctions',
            'rules',
            'users',
            'stats',
            'autoCandidates',
            'month',
            'year',
            'userId',
            'level',
            'status'
        ));
    }

    /**
     * Cari karyawan yang melewati batas keterlambatan atau alpa sesuai Master Aturan Aktif
     */
    private function findAutoViolationCandidates(int $month, int $year): array
    {
        $autoRules = SanctionRule::where('is_active', true)
            ->whereIn('trigger_type', ['AUTO_LATE', 'AUTO_ALPHA'])
            ->whereNotNull('threshold_count')
            ->orderBy('threshold_count', 'desc')
            ->get();

        if ($autoRules->isEmpty()) {
            return [];
        }

        $employees = User::whereDoesntHave('roles', function ($q) {
            $q->where('is_attendance_required', false);
        })->get();

        $candidates = [];

        foreach ($employees as $emp) {
            $attendances = Attendance::where('user_id', $emp->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $lateCount = $attendances->where('is_late', true)->count();
            $alphaCount = $attendances->where('status', 'ALPHA')->count();

            // Cek aturan terlambat tertinggi yang dilanggar
            $matchedLateRule = $autoRules
                ->where('trigger_type', 'AUTO_LATE')
                ->first(fn (SanctionRule $r) => $lateCount > (int) $r->threshold_count);

            if ($matchedLateRule) {
                $alreadyIssued = EmployeeSanction::where('user_id', $emp->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('sanction_rule_id', $matchedLateRule->id)
                    ->exists();

                $candidates[] = [
                    'user' => $emp,
                    'rule' => $matchedLateRule,
                    'count' => $lateCount,
                    'metric_label' => "{$lateCount}x Terlambat (Batas > {$matchedLateRule->threshold_count}x)",
                    'already_issued' => $alreadyIssued,
                ];
            }

            // Cek aturan alpa tertinggi yang dilanggar
            $matchedAlphaRule = $autoRules
                ->where('trigger_type', 'AUTO_ALPHA')
                ->first(fn (SanctionRule $r) => $alphaCount > (int) $r->threshold_count);

            if ($matchedAlphaRule) {
                $alreadyIssued = EmployeeSanction::where('user_id', $emp->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->where('sanction_rule_id', $matchedAlphaRule->id)
                    ->exists();

                $candidates[] = [
                    'user' => $emp,
                    'rule' => $matchedAlphaRule,
                    'count' => $alphaCount,
                    'metric_label' => "{$alphaCount}x Alpa (Batas > {$matchedAlphaRule->threshold_count}x)",
                    'already_issued' => $alreadyIssued,
                ];
            }
        }

        return $candidates;
    }

    /**
     * Jalankan deteksi otomatis sanksi dari data presensi bulan terpilih
     */
    public function detectAutoSanctions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        $candidates = $this->findAutoViolationCandidates($month, $year);
        $createdCount = 0;

        foreach ($candidates as $cand) {
            /** @var User $emp */
            $emp = $cand['user'];
            /** @var SanctionRule $rule */
            $rule = $cand['rule'];

            $sanction = EmployeeSanction::firstOrCreate(
                [
                    'user_id' => $emp->id,
                    'sanction_rule_id' => $rule->id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'issued_by' => auth()->id(),
                    'incident_date' => now()->toDateString(),
                    'violation_name' => $rule->name,
                    'level' => $rule->default_level,
                    'source' => 'AUTO_DETECTED',
                    'occurrence_count' => $cand['count'],
                    'deduction_amount' => $rule->default_deduction_amount,
                    'reason' => "Terdeteksi otomatis oleh sistem presensi: {$cand['metric_label']} pada periode {$month}/{$year}.",
                    'status' => 'ACTIVE',
                ]
            );

            if ($sanction->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $sanction->update(['occurrence_count' => $cand['count']]);
            }
        }

        $periodName = Carbon::create($year, $month, 1)->translatedFormat('F Y');
        if ($createdCount > 0) {
            return redirect()->route('sanctions.index', ['month' => $month, 'year' => $year])
                ->with('success', "Deteksi otomatis periode {$periodName} selesai: {$createdCount} sanksi pelanggaran presensi berhasil diterbitkan.");
        }

        return redirect()->route('sanctions.index', ['month' => $month, 'year' => $year])
            ->with('success', "Deteksi otomatis periode {$periodName} selesai: Tidak ditemukan pelanggaran baru yang belum tercatat.");
    }

    /**
     * Catat pemberian sanksi karyawan secara manual
     */
    public function storeSanction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'sanction_rule_id' => 'nullable|uuid|exists:sanction_rules,id',
            'custom_violation_name' => 'nullable|string|max:255',
            'incident_date' => 'required|date',
            'level' => 'required|in:TEGURAN_LISAN,SP1,SP2,SP3',
            'deduction_amount' => 'required|numeric|min:0',
            'occurrence_count' => 'nullable|integer|min:1',
            'reason' => 'required|string|max:1000',
        ]);

        $rule = !empty($validated['sanction_rule_id'])
            ? SanctionRule::find($validated['sanction_rule_id'])
            : null;

        $violationName = $validated['custom_violation_name'] ?: ($rule?->name ?? 'Pelanggaran Kedisiplinan Kerja');
        $incidentDate = Carbon::parse($validated['incident_date']);

        EmployeeSanction::create([
            'user_id' => $validated['user_id'],
            'sanction_rule_id' => $rule?->id,
            'issued_by' => auth()->id(),
            'month' => (int) $incidentDate->month,
            'year' => (int) $incidentDate->year,
            'incident_date' => $incidentDate->toDateString(),
            'violation_name' => $violationName,
            'level' => $validated['level'],
            'source' => 'MANUAL',
            'occurrence_count' => (int) ($validated['occurrence_count'] ?? 1),
            'deduction_amount' => (float) $validated['deduction_amount'],
            'reason' => $validated['reason'],
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('sanctions.index', [
            'month' => (int) $incidentDate->month,
            'year' => (int) $incidentDate->year,
        ])->with('success', 'Sanksi kedisiplinan karyawan berhasil dicatat.');
    }

    /**
     * Perbarui status atau detail sanksi karyawan
     */
    public function updateSanction(Request $request, EmployeeSanction $employeeSanction): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'required|in:TEGURAN_LISAN,SP1,SP2,SP3',
            'deduction_amount' => 'required|numeric|min:0',
            'status' => 'required|in:ACTIVE,RESOLVED,REVOKED',
            'reason' => 'required|string|max:1000',
        ]);

        $employeeSanction->update($validated);

        return back()->with('success', 'Status dan rincian sanksi karyawan berhasil diperbarui.');
    }

    /**
     * Tambah Master Aturan Jenis Pelanggaran Baru (Manual Configurable Rules)
     */
    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:sanction_rules,code',
            'name' => 'required|string|max:255',
            'category' => 'required|in:DISIPLIN_WAKTU,KEHADIRAN,KINERJA_TUGAS,ETIKA_SOP',
            'trigger_type' => 'required|in:AUTO_LATE,AUTO_ALPHA,MANUAL',
            'threshold_count' => 'nullable|integer|min:1|max:31',
            'default_level' => 'required|in:TEGURAN_LISAN,SP1,SP2,SP3',
            'default_deduction_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active', true);
        if ($validated['trigger_type'] === 'MANUAL') {
            $validated['threshold_count'] = null;
        }

        SanctionRule::create($validated);

        return back()->with('success', "Aturan jenis pelanggaran '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Ubah Master Aturan Jenis Pelanggaran
     */
    public function updateRule(Request $request, SanctionRule $sanctionRule): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:DISIPLIN_WAKTU,KEHADIRAN,KINERJA_TUGAS,ETIKA_SOP',
            'trigger_type' => 'required|in:AUTO_LATE,AUTO_ALPHA,MANUAL',
            'threshold_count' => 'nullable|integer|min:1|max:31',
            'default_level' => 'required|in:TEGURAN_LISAN,SP1,SP2,SP3',
            'default_deduction_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        if ($validated['trigger_type'] === 'MANUAL') {
            $validated['threshold_count'] = null;
        }

        $sanctionRule->update($validated);

        return back()->with('success', "Aturan jenis pelanggaran '{$sanctionRule->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Master Aturan Jenis Pelanggaran
     */
    public function destroyRule(SanctionRule $sanctionRule): RedirectResponse
    {
        $name = $sanctionRule->name;
        $sanctionRule->delete();

        return back()->with('success', "Aturan jenis pelanggaran '{$name}' berhasil dihapus.");
    }

    /**
     * Unduh atau Pratinjau PDF Surat Peringatan (SP1/SP2/SP3/Teguran)
     */
    public function downloadPdf(EmployeeSanction $employeeSanction, Request $request): Response
    {
        $employeeSanction->loadMissing(['user', 'rule', 'issuer']);
        $user = $employeeSanction->user;

        $officeSettings = [
            'name' => Setting::getValue('office_name', 'PT ChronoHR Teknologi Indonesia'),
            'address' => Setting::getValue('office_address', 'Gedung Pusat ChronoHR, Jakarta'),
        ];

        $pdf = Pdf::loadView('sanctions.sp_pdf', [
            'sanction' => $employeeSanction,
            'user' => $user,
            'officeSettings' => $officeSettings,
        ])->setPaper('a4', 'portrait');

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '_', $user->name ?? 'Karyawan');
        $filename = "Surat_Sanksi_{$employeeSanction->level}_{$cleanName}_{$employeeSanction->month}_{$employeeSanction->year}.pdf";

        if ($request->boolean('preview')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }
}
