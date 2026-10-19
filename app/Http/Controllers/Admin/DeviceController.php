<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
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

        $devices = $query->latest('updated_at')->paginate(10)->withQueryString();

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
}
