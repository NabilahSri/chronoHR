<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\EmployeeSanction;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\ProjectIncentive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $currentMonth = (int) now()->month;
        $currentYear = (int) now()->year;
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        // 1. ANALITIK KEUANGAN & PENGELUARAN GAJI (FINANCIAL OVERVIEW)
        $currentMonthPayrolls = Payroll::with('user')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get();

        // Jika bulan berjalan belum ada kalkulasi payroll, gunakan bulan terakhir yang memiliki data payroll sebagai acuan analitik
        $activePayrollMonth = $currentMonth;
        $activePayrollYear = $currentYear;
        if ($currentMonthPayrolls->isEmpty()) {
            $latestPayroll = Payroll::orderBy('year', 'desc')->orderBy('month', 'desc')->first();
            if ($latestPayroll) {
                $activePayrollMonth = (int) $latestPayroll->month;
                $activePayrollYear = (int) $latestPayroll->year;
                $currentMonthPayrolls = Payroll::with('user')
                    ->where('month', $activePayrollMonth)
                    ->where('year', $activePayrollYear)
                    ->get();
            }
        }

        $activeMonthIncentives = (float) ProjectIncentive::whereMonth('given_date', $activePayrollMonth)
            ->whereYear('given_date', $activePayrollYear)
            ->sum('amount');

        $activeMonthOvertime = (float) $currentMonthPayrolls
            ->filter(fn ($p) => ($p->user->employment_type ?? '') !== 'INTERN')
            ->sum('overtime_pay');

        $activeMonthBasicSalary = (float) $currentMonthPayrolls
            ->filter(fn ($p) => ($p->user->employment_type ?? '') !== 'INTERN')
            ->sum(fn ($p) => (float) ($p->user->basic_salary ?? 0));

        if ($activeMonthBasicSalary <= 0) {
            $activeMonthBasicSalary = (float) User::whereIn('employment_type', ['KARTAP', 'PKWT'])->sum('basic_salary');
        }

        $activeMonthNetTotal = (float) $currentMonthPayrolls->sum('net_salary');

        // Bandingkan dengan bulan sebelumnya untuk menghitung persentase tren (MoM)
        $prevDate = Carbon::create($activePayrollYear, $activePayrollMonth, 1)->subMonth();
        $prevMonthPayrolls = Payroll::where('month', (int) $prevDate->month)
            ->where('year', (int) $prevDate->year)
            ->get();
        $prevMonthNetTotal = (float) $prevMonthPayrolls->sum('net_salary');

        $payrollTrendPct = 0.0;
        if ($prevMonthNetTotal > 0) {
            $payrollTrendPct = round((($activeMonthNetTotal - $prevMonthNetTotal) / $prevMonthNetTotal) * 100, 1);
        }

        // Grafik Analitik Keuangan 6 Bulan Terakhir
        $financialTrend = [];
        $maxChartValue = 1;

        for ($i = 5; $i >= 0; $i--) {
            $datePoint = Carbon::create($activePayrollYear, $activePayrollMonth, 1)->subMonths($i);
            $m = (int) $datePoint->month;
            $y = (int) $datePoint->year;

            $periodPayrolls = Payroll::with('user')
                ->where('month', $m)
                ->where('year', $y)
                ->get();

            $netSalarySum = (float) $periodPayrolls->sum('net_salary');
            $basicSalarySum = (float) $periodPayrolls
                ->filter(fn ($p) => ($p->user->employment_type ?? '') !== 'INTERN')
                ->sum(fn ($p) => (float) ($p->user->basic_salary ?? 0));
            $overtimeSum = (float) $periodPayrolls
                ->filter(fn ($p) => ($p->user->employment_type ?? '') !== 'INTERN')
                ->sum('overtime_pay');
            $incentiveSum = (float) ProjectIncentive::whereMonth('given_date', $m)
                ->whereYear('given_date', $y)
                ->sum('amount');

            if ($netSalarySum > $maxChartValue) {
                $maxChartValue = $netSalarySum;
            }

            $financialTrend[] = [
                'label' => $datePoint->translatedFormat('M Y'),
                'month' => $m,
                'year' => $y,
                'basic_salary' => $basicSalarySum,
                'net_salary' => $netSalarySum,
                'overtime_pay' => $overtimeSum,
                'incentives' => $incentiveSum,
                'published_count' => $periodPayrolls->where('status', 'PUBLISHED')->count(),
                'total_count' => $periodPayrolls->count(),
            ];
        }

        // 2. ANALITIK KEDISIPLINAN & TINGKAT KEHADIRAN (ATTENDANCE ANALYTICS)
        $allAttendances = Attendance::all();
        $totalAttendanceRecords = max(1, $allAttendances->count());
        $onsiteCount = $allAttendances->where('status', 'HADIR_ON_SITE')->count();
        $wfaCount = $allAttendances->where('status', 'WFA')->count();
        $sakitIzinAlphaCount = $allAttendances->whereIn('status', ['SAKIT', 'IZIN', 'ALPHA'])->count();
        $lateCount = $allAttendances->where('is_late', true)->count();
        $presentCount = max(1, $onsiteCount + $wfaCount);

        $attendanceAnalytics = [
            'total_records' => $allAttendances->count(),
            'onsite_count' => $onsiteCount,
            'onsite_pct' => $allAttendances->isNotEmpty() ? round(($onsiteCount / $totalAttendanceRecords) * 100, 1) : 0,
            'wfa_count' => $wfaCount,
            'wfa_pct' => $allAttendances->isNotEmpty() ? round(($wfaCount / $totalAttendanceRecords) * 100, 1) : 0,
            'absence_count' => $sakitIzinAlphaCount,
            'absence_pct' => $allAttendances->isNotEmpty() ? round(($sakitIzinAlphaCount / $totalAttendanceRecords) * 100, 1) : 0,
            'late_count' => $lateCount,
            'discipline_pct' => ($onsiteCount + $wfaCount) > 0
                ? round((($presentCount - $lateCount) / $presentCount) * 100, 1)
                : 100,
            'late_pct' => ($onsiteCount + $wfaCount) > 0
                ? round(($lateCount / $presentCount) * 100, 1)
                : 0,
            'active_sanctions_count' => EmployeeSanction::where('status', 'ACTIVE')->count(),
            'active_sp_count' => EmployeeSanction::where('status', 'ACTIVE')->whereIn('level', ['SP1', 'SP2', 'SP3'])->count(),
        ];

        // 3. EVALUASI PRODUKTIVITAS & PENYELESAIAN TUGAS (TASK COMPLETION ANALYTICS)
        $obligatedEmployeesCount = User::whereDoesntHave('roles', function ($q) {
            $q->where('is_attendance_required', false);
        })->count();

        $allDailyReports = DailyReport::all();
        $completedTaskItems = 0;
        $pendingTaskItems = 0;
        $additionalTaskItems = 0;

        foreach ($allDailyReports as $dr) {
            if (!empty($dr->evening_completed_tasks)) {
                $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $dr->evening_completed_tasks)));
                $completedTaskItems += count($lines);
            }
            if (!empty($dr->evening_pending_tasks)) {
                $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $dr->evening_pending_tasks)));
                $pendingTaskItems += count($lines);
            }
            if (!empty($dr->evening_additional_tasks)) {
                $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $dr->evening_additional_tasks)));
                $additionalTaskItems += count($lines);
            }
        }

        $totalTrackedTasks = max(1, $completedTaskItems + $pendingTaskItems);
        $taskCompletionPct = ($completedTaskItems + $pendingTaskItems) > 0
            ? round(($completedTaskItems / $totalTrackedTasks) * 100, 1)
            : 0;

        $todayReports = DailyReport::whereDate('date', $today)->get();
        $taskAnalytics = [
            'obligated_staff' => $obligatedEmployeesCount,
            'today_submitted' => $todayReports->count(),
            'today_completed_reports' => $todayReports->where('status', 'COMPLETED')->count(),
            'completed_items' => $completedTaskItems,
            'pending_items' => $pendingTaskItems,
            'additional_items' => $additionalTaskItems,
            'completion_rate_pct' => $taskCompletionPct,
        ];

        $stats = [
            'total_employees' => User::count(),
            'kartap_count' => User::where('employment_type', 'KARTAP')->count(),
            'pkwt_count' => User::where('employment_type', 'PKWT')->count(),
            'intern_count' => User::where('employment_type', 'INTERN')->count(),
            'today_onsite' => Attendance::where('date', $today)->where('status', 'HADIR_ON_SITE')->count(),
            'today_wfa' => Attendance::where('date', $today)->where('status', 'WFA')->count(),
            'today_leave' => Attendance::where('date', $today)->whereIn('status', ['SAKIT', 'IZIN'])->count(),
            'pending_leaves' => LeaveRequest::where('status', 'PENDING')->count(),
            'latest_payroll_expense' => $activeMonthNetTotal,
            'active_payroll_period' => Carbon::create($activePayrollYear, $activePayrollMonth, 1)->translatedFormat('F Y'),
            'total_month_basic_salary' => $activeMonthBasicSalary,
            'total_month_payroll' => $activeMonthNetTotal,
            'total_month_overtime' => $activeMonthOvertime,
            'total_month_incentive' => $activeMonthIncentives,
            'payroll_trend_pct' => $payrollTrendPct,
        ];

        $recentAttendances = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('executive.dashboard', compact(
            'stats',
            'financialTrend',
            'maxChartValue',
            'attendanceAnalytics',
            'taskAnalytics',
            'recentAttendances'
        ));
    }
}
