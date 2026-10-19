@extends('layouts.app')

@section('title', 'Status & Maintenance Alat IoT — ChronoHR')
@section('page_title', 'Status & Pemeliharaan Perangkat IoT')
@section('page_subtitle', 'Pantau scanner absensi RFID, konfigurasi mode registrasi kartu, dan status operasional perangkat.')

@section('page_actions')
    <button type="button" onclick="document.getElementById('modal-add-device').classList.remove('hidden')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Daftarkan Alat Baru</span>
    </button>
@endsection

@section('content')
<div class="space-y-8">

    <!-- 4 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Devices -->
        <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Alat Terpasang</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-mono font-bold text-xs">
                    IoT
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total'] }}</div>
            <p class="text-xs text-slate-500 mt-2">Unit NodeMCU scanner aktif terdaftar</p>
        </div>

        <!-- Card 2: Online Devices -->
        <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Status Online</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-emerald-600 font-mono">{{ $stats['online'] }}</div>
            <p class="text-xs text-slate-500 mt-2">Heartbeat terdeteksi < 5 menit terakhir</p>
        </div>

        <!-- Card 3: Maintenance Mode -->
        <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Mode Maintenance</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-amber-600 font-mono">{{ $stats['maintenance'] }}</div>
            <p class="text-xs text-slate-500 mt-2">Diisolasi untuk kalibrasi / servis</p>
        </div>

        <!-- Card 4: Operating Modes -->
        <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Mode Operasi</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
            <div class="text-lg font-bold text-slate-900 font-mono">
                {{ $stats['attendance_mode'] }} Absensi • {{ $stats['registration_mode'] }} Registrasi
            </div>
            <p class="text-xs text-slate-500 mt-2">Dapat diubah real-time dari kontrol di bawah</p>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-5 shadow-xs backdrop-blur-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.devices.index') }}" class="flex-1 flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[240px] group">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari nama alat, UID (ESP8266-...), IP, lokasi..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-auto min-w-[150px]">
                <select name="status"
                    class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="ONLINE" {{ $status === 'ONLINE' ? 'selected' : '' }}>🟢 Online</option>
                    <option value="OFFLINE" {{ $status === 'OFFLINE' ? 'selected' : '' }}>🔴 Offline</option>
                    <option value="MAINTENANCE" {{ $status === 'MAINTENANCE' ? 'selected' : '' }}>🟡 Maintenance</option>
                </select>
            </div>

            <!-- Mode Filter -->
            <div class="w-full sm:w-auto min-w-[160px]">
                <select name="mode"
                    class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all cursor-pointer">
                    <option value="">Semua Mode Operasi</option>
                    <option value="ATTENDANCE" {{ $mode === 'ATTENDANCE' ? 'selected' : '' }}>Mode Absensi</option>
                    <option value="REGISTRATION" {{ $mode === 'REGISTRATION' ? 'selected' : '' }}>Mode Registrasi</option>
                    <option value="MAINTENANCE" {{ $mode === 'MAINTENANCE' ? 'selected' : '' }}>Mode Maintenance</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold shadow-xs transition-all cursor-pointer">
                    Filter
                </button>
                @if ($search || $status || $mode)
                    <a href="{{ route('admin.devices.index') }}"
                        class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Device List Table Card -->
    <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Daftar Perangkat NodeMCU RFID</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tabel operasional scanner RFID, konfigurasi mode kerja, dan pemeliharaan alat.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200 shrink-0 self-start sm:self-auto">
                Total: {{ $devices->total() }} Perangkat
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Perangkat & UID</th>
                        <th scope="col" class="px-6 py-4">Lokasi & Jaringan</th>
                        <th scope="col" class="px-6 py-4">Status Alat</th>
                        <th scope="col" class="px-6 py-4">Mode Operasi</th>
                        <th scope="col" class="px-6 py-4">Heartbeat Terakhir</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi & Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($devices as $device)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Perangkat & UID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-mono font-bold text-xs shrink-0 border border-slate-200">
                                        IoT
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-sm leading-tight truncate">{{ $device->name }}</p>
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                                UID: {{ $device->device_uid }}
                                            </span>
                                            @if($device->notes)
                                                <span class="text-slate-400 cursor-help" title="{{ $device->notes }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Lokasi & Jaringan -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs">
                                    <p class="font-semibold text-slate-800">{{ $device->location }}</p>
                                    <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                        IP: {{ $device->ip_address ?: 'Belum terdeteksi' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        FW: v{{ $device->firmware_version }}
                                    </p>
                                </div>
                            </td>

                            <!-- Status Alat -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($device->status === 'MAINTENANCE')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        MAINTENANCE
                                    </span>
                                @elseif($device->isOnline())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        ONLINE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        OFFLINE
                                    </span>
                                @endif
                            </td>

                            <!-- Mode Operasi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.devices.mode', $device) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="mode" onchange="this.form.submit()"
                                        class="text-xs font-semibold rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-slate-700 shadow-2xs focus:border-sky-500 focus:outline-hidden cursor-pointer">
                                        <option value="ATTENDANCE" {{ $device->mode === 'ATTENDANCE' ? 'selected' : '' }}>Mode Absensi</option>
                                        <option value="REGISTRATION" {{ $device->mode === 'REGISTRATION' ? 'selected' : '' }}>Mode Registrasi</option>
                                        <option value="MAINTENANCE" {{ $device->mode === 'MAINTENANCE' ? 'selected' : '' }}>Mode Maintenance</option>
                                    </select>
                                </form>
                            </td>

                            <!-- Heartbeat Terakhir -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-mono">
                                    <p class="text-slate-800 font-semibold">{{ $device->last_ping_at ? $device->last_ping_at->diffForHumans() : 'Belum pernah' }}</p>
                                    @if($device->last_ping_at)
                                        <p class="text-[10px] text-slate-400">{{ $device->last_ping_at->format('d M Y, H:i') }} WIB</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Aksi & Kontrol -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <!-- Toggle Maintenance Button -->
                                    <form action="{{ route('admin.devices.maintenance', $device) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($device->status === 'MAINTENANCE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-2xs"
                                                title="Selesaikan servis dan aktifkan alat kembali">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Aktifkan</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-2xs"
                                                title="Isolasi alat untuk perbaikan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Servis</span>
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" onsubmit="return confirm('Hapus perangkat {{ $device->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Perangkat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400 text-xs">
                                <p class="font-medium text-slate-600">Tidak ada perangkat IoT yang sesuai kriteria</p>
                                <p class="text-[11px] text-slate-400 mt-1">Coba ubah kata kunci pencarian atau reset filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $devices->links() }}
            </div>
        @endif
    </div>

    <!-- Integration Hint Panel -->
    <div class="bg-slate-900 text-slate-200 rounded-3xl p-6 border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs font-mono">
        <div>
            <div class="text-sky-400 font-bold mb-1">Endpoints IoT Sync NodeMCU:</div>
            <div>POST <span class="text-white bg-slate-800 px-2 py-0.5 rounded">{{ url('/devices/heartbeat') }}</span></div>
            <div class="text-slate-400 text-[11px] mt-1">Payload JSON: { "device_uid": "ESP8266-LOBBY-01" }</div>
        </div>
        <div class="text-slate-400 text-[11px] max-w-sm">
            NodeMCU otomatis memperbarui timestamp last_ping dan mengambil status mode (ATTENDANCE / REGISTRATION / MAINTENANCE) dalam respon JSON.
        </div>
    </div>

</div>

<!-- Modal Add Device -->
<div id="modal-add-device" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">Registrasi Perangkat IoT Baru</h3>
            <button type="button" onclick="document.getElementById('modal-add-device').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('admin.devices.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Perangkat</label>
                <input type="text" name="name" required placeholder="Contoh: NodeMCU Scanner Pintu Depan"
                    class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Device UID / Chip ID</label>
                    <input type="text" name="device_uid" required placeholder="ESP8266-01"
                        class="w-full text-xs font-mono rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat IP (Opsional)</label>
                    <input type="text" name="ip_address" placeholder="192.168.1.100"
                        class="w-full text-xs font-mono rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lokasi Pemasangan</label>
                    <input type="text" name="location" required placeholder="Lobby Gedung A"
                        class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Versi Firmware</label>
                    <input type="text" name="firmware_version" value="1.0.0"
                        class="w-full text-xs font-mono rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Catatan maintenance atau spesifikasi hardware..."
                    class="w-full text-xs rounded-xl border border-slate-300 p-2.5 focus:border-sky-500 focus:outline-hidden"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-add-device').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2 text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow-xs cursor-pointer">
                    Simpan Perangkat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
