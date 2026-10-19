<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $today = now()->toDateString();

        $query = Attendance::with(['user.roles'])->whereDate('date', $today);

        // Pencarian murni berdasarkan nama karyawan
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Statistik presensi hari ini
        $statsToday = Attendance::whereDate('date', $today);
        $stats = [
            'total' => (clone $statsToday)->count(),
            'onsite' => (clone $statsToday)->where('status', 'HADIR_ON_SITE')->count(),
            'wfh' => (clone $statsToday)->where('status', 'WFH')->count(),
            'late' => (clone $statsToday)->where('is_late', true)->count(),
            'leaves' => (clone $statsToday)->whereIn('status', ['SAKIT', 'IZIN'])->count(),
        ];

        $attendances = $query->orderBy('time_in', 'desc')->paginate(15)->withQueryString();

        return view('admin.attendances.index', compact(
            'attendances',
            'stats',
            'search',
            'today'
        ));
    }
}
