<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\Device;
use App\Models\Setting;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $allDevices = Device::all();

        $stats = [
            'total' => $allDevices->count(),
            'online' => $allDevices->filter(fn(Device $d) => $d->isOnline())->count(),
            'maintenance' => $allDevices->where('status', 'MAINTENANCE')->count(),
            'attendance_mode' => $allDevices->where('mode', 'ATTENDANCE')->count(),
            'registration_mode' => $allDevices->where('mode', 'REGISTRATION')->count(),
        ];

        $search = $request->input('search');
        $status = $request->input('status');
        $mode = $request->input('mode');

        $query = Device::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('device_uid', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($status) {
            if ($status === 'MAINTENANCE') {
                $query->where('status', 'MAINTENANCE');
            } elseif ($status === 'ONLINE') {
                $query->where('status', '!=', 'MAINTENANCE')
                    ->where('last_ping_at', '>=', now()->subMinutes(5));
            } elseif ($status === 'OFFLINE') {
                $query->where('status', '!=', 'MAINTENANCE')
                    ->where(function ($q) {
                        $q->whereNull('last_ping_at')
                            ->orWhere('last_ping_at', '<', now()->subMinutes(5));
                    });
            }
        }

        if ($mode) {
            $query->where('mode', $mode);
        }

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $devices = $query->latest('updated_at')->paginate($perPage)->withQueryString();

        return view('admin.devices.index', compact('devices', 'stats', 'search', 'status', 'mode'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_uid' => 'required|string|max:100|unique:devices,device_uid',
            'ip_address' => 'nullable|ip',
            'location' => 'required|string|max:255',
            'firmware_version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        Device::create($validated);

        return back()->with('success', 'Perangkat IoT baru berhasil ditambahkan.');
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_uid' => "required|string|max:100|unique:devices,device_uid,{$device->id}",
            'ip_address' => 'nullable|ip',
            'location' => 'required|string|max:255',
            'firmware_version' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $device->update($validated);

        return back()->with('success', "Data perangkat {$device->name} berhasil diperbarui.");
    }

    public function toggleMaintenance(Device $device): RedirectResponse
    {
        $device->toggleMaintenance();

        $statusMsg = $device->status === 'MAINTENANCE'
            ? "Mode Maintenance diaktifkan untuk {$device->name}. Perangkat sementara dinonaktifkan dari absensi."
            : "Mode Maintenance dinonaktifkan untuk {$device->name}. Perangkat kembali aktif normal.";

        return back()->with('success', $statusMsg);
    }

    public function setMode(Request $request, Device $device): RedirectResponse
    {
        $request->validate([
            'mode' => 'required|in:ATTENDANCE,REGISTRATION,MAINTENANCE',
        ]);

        $mode = $request->input('mode');
        $device->setMode($mode);

        $modeLabel = [
            'ATTENDANCE' => 'Mode Absensi Tap Masuk/Pulang',
            'REGISTRATION' => 'Mode Pairing/Registrasi Kartu RFID Baru',
            'MAINTENANCE' => 'Mode Pemeliharaan / Diagnostic',
        ][$mode] ?? $mode;

        return back()->with('success', "Mode operasional {$device->name} berhasil diubah ke: {$modeLabel}.");
    }

    public function destroy(Device $device): RedirectResponse
    {
        $name = $device->name;
        $device->delete();

        return back()->with('success', "Perangkat {$name} berhasil dihapus dari sistem.");
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_uid' => 'required|string',
        ]);

        $device = Device::firstOrCreate(
            ['device_uid' => $validated['device_uid']],
            [
                'name' => "NodeMCU " . substr($validated['device_uid'], -6),
                'location' => 'Belum Ditentukan',
                'status' => 'ONLINE',
                'mode' => 'ATTENDANCE',
                'last_ping_at' => now(),
            ]
        );

        $device->recordHeartbeat($request->ip());

        return response()->json([
            'status' => 'OK',
            'device_uid' => $device->device_uid,
            'operational_status' => $device->status,
            'mode' => $device->mode,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Endpoint saat kartu fisik RFID ditempelkan (Tap) pada perangkat NodeMCU
     * Mendukung Mode PRESENSI (Masuk/Pulang) dan Mode REGISTRASI (Pairing Kartu Baru)
     */
    public function rfidTap(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_uid' => 'required|string',
            'rfid_uid' => 'required|string',
        ]);

        $device = Device::firstOrCreate(
            ['device_uid' => $validated['device_uid']],
            [
                'name' => 'NodeMCU ' . substr($validated['device_uid'], -6),
                'location' => 'Gerbang Utama',
                'status' => 'ONLINE',
                'mode' => 'ATTENDANCE',
                'last_ping_at' => now(),
            ]
        );

        $device->recordHeartbeat($request->ip());
        $rfidUid = trim($validated['rfid_uid']);

        if ($device->status === 'MAINTENANCE' || $device->mode === 'MAINTENANCE') {
            return response()->json([
                'success' => false,
                'status' => 'MAINTENANCE',
                'message' => "Perangkat {$device->name} sedang dalam mode pemeliharaan.",
            ], 503);
        }

        // Simpan pindaian terakhir agar dapat diambil otomatis pada form Registrasi Karyawan
        Setting::setValue('last_scanned_rfid_uid', $rfidUid);
        Setting::setValue('last_scanned_rfid_device', $device->name . ' (' . $device->device_uid . ')');
        Setting::setValue('last_scanned_rfid_at', now()->format('Y-m-d H:i:s'));

        // 1. Jika Perangkat dalam Mode REGISTRASI KARTU BARU
        if ($device->mode === 'REGISTRATION') {
            $existingOwner = User::where('rfid_uid', $rfidUid)->first();

            return response()->json([
                'success' => true,
                'status' => 'REGISTRATION_SCANNED',
                'mode' => 'REGISTRATION',
                'rfid_uid' => $rfidUid,
                'device_uid' => $device->device_uid,
                'already_assigned_to' => $existingOwner?->name,
                'message' => $existingOwner
                    ? "Kartu RFID {$rfidUid} terpindai (Saat ini terdaftar milik {$existingOwner->name})."
                    : "Kartu RFID {$rfidUid} berhasil dipindai dan siap didaftarkan ke karyawan baru.",
            ]);
        }

        // 2. Jika Perangkat dalam Mode PRESENSI (ATTENDANCE)
        $user = User::where('rfid_uid', $rfidUid)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'status' => 'UNREGISTERED_CARD',
                'rfid_uid' => $rfidUid,
                'message' => "Kartu RFID {$rfidUid} belum terdaftar pada karyawan mana pun.",
            ], 404);
        }

        $today = now()->toDateString();
        $now = now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $attendanceService = app(AttendanceService::class);

        // A. Tap Pertama Pagi -> Presensi Masuk (Check-In)
        if (!$attendance || !$attendance->time_in) {
            $isLate = $attendanceService->isLateAt($now);

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date' => $today,
                ],
                [
                    'time_in' => $now->format('H:i:s'),
                    'status' => 'HADIR_ON_SITE',
                    'method' => 'RFID',
                    'is_late' => $isLate,
                ]
            );

            return response()->json([
                'success' => true,
                'status' => 'CHECK_IN_SUCCESS',
                'action' => 'check_in',
                'user_name' => $user->name,
                'rfid_uid' => $rfidUid,
                'time_in' => $now->format('H:i:s'),
                'is_late' => $isLate,
                'message' => "Selamat datang, {$user->name}. Presensi masuk tercatat pukul {$now->format('H:i')} WIB.",
            ]);
        }

        // B. Tap Kedua Sore -> Presensi Pulang (Check-Out)
        if (!$attendance->time_out) {
            if ($user->isAttendanceRequired()) {
                $dailyReport = DailyReport::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->first();

                if (!$dailyReport || $dailyReport->status !== 'COMPLETED') {
                    return response()->json([
                        'success' => false,
                        'status' => 'DAILY_REPORT_INCOMPLETE',
                        'action' => 'check_out_blocked',
                        'user_name' => $user->name,
                        'message' => "Halo {$user->name}, silakan lengkapi evaluasi laporan tugas sore (Daily Report) terlebih dahulu sebelum tap pulang.",
                    ], 422);
                }
            }

            $overtimeHours = $attendanceService->calculateOvertimeHours($now);

            $attendance->update([
                'time_out' => $now->format('H:i:s'),
                'overtime_hours' => $overtimeHours,
            ]);

            return response()->json([
                'success' => true,
                'status' => 'CHECK_OUT_SUCCESS',
                'action' => 'check_out',
                'user_name' => $user->name,
                'rfid_uid' => $rfidUid,
                'time_out' => $now->format('H:i:s'),
                'overtime_hours' => $overtimeHours,
                'message' => "Terima kasih, {$user->name}. Presensi pulang tercatat pukul {$now->format('H:i')} WIB.",
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 'ALREADY_COMPLETED',
            'action' => 'already_completed',
            'user_name' => $user->name,
            'message' => "Presensi masuk dan pulang {$user->name} hari ini sudah lengkap.",
        ], 422);
    }

    /**
     * Ambil UID Kartu RFID terakhir yang baru ditempelkan ke alat NodeMCU
     */
    public function latestScan(): JsonResponse
    {
        $uid = Setting::getValue('last_scanned_rfid_uid', '');
        $device = Setting::getValue('last_scanned_rfid_device', '');
        $scannedAt = Setting::getValue('last_scanned_rfid_at', '');

        return response()->json([
            'success' => true,
            'rfid_uid' => $uid,
            'device' => $device,
            'scanned_at' => $scannedAt,
        ]);
    }
}
