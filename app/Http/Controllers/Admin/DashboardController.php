<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        $totalUsers = User::count();
        $rfidAssigned = User::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->count();
        $rfidUnassigned = $totalUsers - $rfidAssigned;
        $approverCount = User::where('can_approve_leaves', true)->count();

        $devices = Device::all();

        $stats = [
            'total_users' => $totalUsers,
            'rfid_assigned' => $rfidAssigned,
            'rfid_unassigned' => $rfidUnassigned,
            'approvers_count' => $approverCount,
            'kartap' => User::where('employment_type', 'KARTAP')->count(),
            'pkwt' => User::where('employment_type', 'PKWT')->count(),
            'intern' => User::where('employment_type', 'INTERN')->count(),
            'today_onsite' => Attendance::where('date', $today)->where('status', 'HADIR_ON_SITE')->count(),
            'today_wfh' => Attendance::where('date', $today)->where('status', 'WFH')->count(),
            'today_late' => Attendance::where('date', $today)->where('is_late', true)->count(),
            'total_devices' => $devices->count(),
            'online_devices' => $devices->filter(fn(Device $d) => $d->isOnline())->count(),
            'maintenance_devices' => $devices->where('status', 'MAINTENANCE')->count(),
            'pending_leaves_count' => LeaveRequest::where('status', 'PENDING')->count(),
        ];

        $settings = Setting::pluck('value', 'key')->toArray();
        $tolerances = [
            'late' => $settings['late_tolerance_minutes'] ?? '15',
            'overtime' => $settings['overtime_tolerance_minutes'] ?? '30',
        ];

        $recentUsers = User::with('roles')->latest()->take(6)->get();

        $recentAttendances = Attendance::with('user')
            ->where('date', $today)
            ->latest('updated_at')
            ->take(6)
            ->get();

        $recentLeaves = LeaveRequest::with('user')
            ->latest()
            ->take(5)
            ->get();

        $devicesList = $devices->take(4);

        return view('admin.dashboard', compact(
            'stats',
            'tolerances',
            'recentUsers',
            'recentAttendances',
            'recentLeaves',
            'devicesList'
        ));
    }
}
