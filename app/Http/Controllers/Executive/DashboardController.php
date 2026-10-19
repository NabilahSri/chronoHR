<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $stats = [
            'total_employees' => User::count(),
            'kartap_count' => User::where('employment_type', 'KARTAP')->count(),
            'pkwt_count' => User::where('employment_type', 'PKWT')->count(),
            'intern_count' => User::where('employment_type', 'INTERN')->count(),
            'today_onsite' => Attendance::where('date', $today)->where('status', 'HADIR_ON_SITE')->count(),
            'today_wfh' => Attendance::where('date', $today)->where('status', 'WFH')->count(),
            'today_leave' => Attendance::where('date', $today)->whereIn('status', ['SAKIT', 'IZIN'])->count(),
            'pending_leaves' => LeaveRequest::where('status', 'PENDING')->count(),
            'latest_payroll_expense' => Payroll::where('status', 'PUBLISHED')
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->sum('net_salary'),
        ];

        $recentAttendances = Attendance::with('user')
            ->where('date', $today)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('executive.dashboard', compact('stats', 'recentAttendances'));
    }
}
