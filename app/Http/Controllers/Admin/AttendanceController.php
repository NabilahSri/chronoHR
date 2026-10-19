<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

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

        // Data titik acuan kantor untuk visualisasi geofence
        $officeSettings = $this->attendanceService->getOfficeSettings();

        // Daftar personil untuk modal presensi mandiri / simulasi
        $users = User::select('id', 'name', 'employment_type', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.attendances.index', compact(
            'attendances',
            'stats',
            'search',
            'today',
            'officeSettings',
            'users'
        ));
    }

    /**
     * Endpoint untuk presensi berbasis lokasi (GPS Browser / Web Portal / Mobile)
     */
    public function locationCheckIn(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|uuid|exists:users,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'presence_type' => 'required|in:ON_SITE,WFH,AUTO',
            'notes' => 'nullable|string|max:255',
        ]);

        // Tentukan personil yang presensi
        $user = Auth::user();
        if (!empty($validated['user_id'])) {
            // Super Admin dapat mencatatkan presensi personil (misal lupa RFID atau magang)
            if ($user && $user->hasRole('super_admin')) {
                $user = User::findOrFail($validated['user_id']);
            }
        }

        if (!$user) {
            throw ValidationException::withMessages([
                'user' => 'Pengguna tidak terotentikasi.',
            ]);
        }

        try {
            $result = $this->attendanceService->recordLocationAttendance(
                user: $user,
                latitude: (float) $validated['latitude'],
                longitude: (float) $validated['longitude'],
                presenceType: $validated['presence_type'],
                notes: $validated['notes'] ?? null
            );

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'action' => $result['action'],
                    'message' => $result['message'],
                    'distance' => $result['distance'],
                    'is_within_radius' => $result['is_within_radius'],
                    'attendance' => $result['attendance'],
                ]);
            }

            return back()->with('success', $result['message']);
        } catch (ValidationException $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }
}
