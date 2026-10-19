<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
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

        // Ambil data untuk statistik dan tabel
        $attendances = $query->orderBy('date', 'desc')->orderBy('time_in', 'desc')->get();

        $stats = [
            'total' => $attendances->count(),
            'on_site' => $attendances->where('status', 'HADIR_ON_SITE')->count(),
            'wfh' => $attendances->where('status', 'WFH')->count(),
            'sakit_izin' => $attendances->whereIn('status', ['SAKIT', 'IZIN'])->count(),
            'late' => $attendances->where('is_late', true)->count(),
            'total_overtime' => $attendances->sum('overtime_hours'),
        ];

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
     * Laporan 2: Rekapitulasi Slip Gaji & Beban Payroll
     */
    public function payrolls(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $userId = $request->input('user_id');
        $status = $request->input('status');
        $employmentType = $request->input('employment_type');

        $query = Payroll::with('user')
            ->where('month', $month)
            ->where('year', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($employmentType) {
            $query->whereHas('user', function ($q) use ($employmentType) {
                $q->where('employment_type', $employmentType);
            });
        }

        $payrolls = $query->orderBy('net_salary', 'desc')->get();

        $stats = [
            'total_count' => $payrolls->count(),
            'total_net' => (float) $payrolls->sum('net_salary'),
            'total_gross' => (float) $payrolls->sum('gross_salary'),
            'total_wfh_deduction' => (float) $payrolls->sum('wfh_deduction_amount'),
            'total_overtime_pay' => (float) $payrolls->sum('overtime_pay'),
            'published_count' => $payrolls->where('status', 'PUBLISHED')->count(),
            'draft_count' => $payrolls->where('status', 'DRAFT')->count(),
        ];

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('reports.payrolls', compact(
            'payrolls',
            'stats',
            'users',
            'month',
            'year',
            'userId',
            'status',
            'employmentType'
        ));
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

        $leaves = $query->orderBy('start_date', 'desc')->get();

        $stats = [
            'total' => $leaves->count(),
            'approved' => $leaves->where('status', 'APPROVED')->count(),
            'pending' => $leaves->where('status', 'PENDING')->count(),
            'rejected' => $leaves->where('status', 'REJECTED')->count(),
            'wfh_count' => $leaves->where('type', 'WFH')->count(),
            'sakit_count' => $leaves->where('type', 'SAKIT')->count(),
            'izin_count' => $leaves->where('type', 'IZIN')->count(),
        ];

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
}
