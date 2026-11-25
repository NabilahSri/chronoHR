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
        $userId = $request->input('user_id');
        $today = $request->input('date', now()->toDateString());
        $status = $request->input('status');
        $isLate = $request->input('is_late');

        $query = Attendance::with(['user.roles'])->whereDate('date', $today);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Pencarian berdasarkan nama atau email karyawan
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($isLate !== null && $isLate !== '') {
            $query->where('is_late', (bool) $isLate);
        }

        // Statistik presensi pada tanggal terpilih
        $statsToday = Attendance::whereDate('date', $today);
        $stats = [
            'total' => (clone $statsToday)->count(),
            'onsite' => (clone $statsToday)->where('status', 'HADIR_ON_SITE')->count(),
            'wfa' => (clone $statsToday)->where('status', 'WFA')->count(),
            'late' => (clone $statsToday)->where('is_late', true)->count(),
            'leaves' => (clone $statsToday)->whereIn('status', ['SAKIT', 'IZIN'])->count(),
        ];

        $attendances = $query->orderBy('time_in', 'desc')->paginate(15)->withQueryString();
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $attendances = $query->orderBy('time_in', 'desc')->paginate($perPage)->withQueryString();

        // Data titik acuan kantor untuk visualisasi geofence
        $officeSettings = $this->attendanceService->getOfficeSettings();

        // Daftar personil untuk filter & modal presensi mandiri
        $users = User::select('id', 'name', 'employment_type', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.attendances.index', compact(
            'attendances',
            'stats',
            'search',
            'userId',
            'today',
            'status',
            'isLate',
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
            'presence_type' => 'required|in:ON_SITE,WFA,AUTO',
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

    /**
     * Koreksi atau Input Presensi Manual oleh Super Admin (Attendance Override)
     */
    public function manualOverride(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:HADIR_ON_SITE,WFA,SAKIT,IZIN,ALPHA',
            'time_in' => 'nullable|string|max:8',
            'time_out' => 'nullable|string|max:8',
            'is_late' => 'nullable|boolean',
            'overtime_hours' => 'nullable|integer|min:0|max:24',
            'method' => 'nullable|in:RFID,FACIAL_LOCATION,FORM',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $timeIn = !empty($validated['time_in']) ? (strlen($validated['time_in']) === 5 ? $validated['time_in'] . ':00' : $validated['time_in']) : null;
        $timeOut = !empty($validated['time_out']) ? (strlen($validated['time_out']) === 5 ? $validated['time_out'] . ':00' : $validated['time_out']) : null;

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'method' => $validated['method'] ?? ($validated['status'] === 'HADIR_ON_SITE' ? 'RFID' : ($validated['status'] === 'WFA' ? 'FACIAL_LOCATION' : 'FORM')),
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'is_late' => (bool) ($validated['is_late'] ?? false),
                'overtime_hours' => (int) ($validated['overtime_hours'] ?? 0),
            ]
        );

        return redirect()
            ->route('admin.attendances.index', ['date' => $validated['date']])
            ->with('success', "Log presensi manual untuk {$user->name} pada tanggal {$validated['date']} berhasil diperbarui.");
    }
}
