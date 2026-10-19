@extends('layouts.app')

@section('title', 'Dashboard Super Admin — ChronoHR')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
    <div class="space-y-8">

        <!-- 4 Core Control Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Card 1: Total Karyawan & Status -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Karyawan</span>
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total_users'] }}</div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs font-mono">
                    <span
                        class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $stats['kartap'] }}
                        Kartap</span>
                    <span
                        class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $stats['pkwt'] }}
                        PKWT</span>
                    <span
                        class="text-sky-700 font-bold bg-sky-50 px-2 py-0.5 rounded border border-sky-200">{{ $stats['intern'] }}
                        Intern</span>
                </div>
            </div>

            <!-- Card 2: Status Kartu RFID NodeMCU -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Kartu RFID Terdaftar</span>
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-sky-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 font-mono">
                        {{ $stats['rfid_assigned'] }} <span class="text-sm font-normal text-slate-400">/
                            {{ $stats['total_users'] }}</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    @if ($stats['rfid_unassigned'] > 0)
                        <span
                            class="text-amber-700 font-semibold bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                            {{ $stats['rfid_unassigned'] }} Belum Dipairing
                        </span>
                    @else
                        <span
                            class="text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                            ✓ Semua Terpairing
                        </span>
                    @endif
                    <a href="{{ route('admin.users.index') }}" class="text-sky-600 font-semibold hover:underline">Kelola
                        &rarr;</a>
                </div>
            </div>

            <!-- Card 3: Presensi Real-Time Hari Ini -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Presensi Hari Ini</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-emerald-600 font-mono">
                        {{ $stats['today_onsite'] + $stats['today_wfh'] }}
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono">
                    <span class="text-slate-600">Onsite: <strong>{{ $stats['today_onsite'] }}</strong></span>
                    <span class="text-sky-700">WFH: <strong>{{ $stats['today_wfh'] }}</strong></span>
                    <span class="text-rose-600">Terlambat: <strong>{{ $stats['today_late'] }}</strong></span>
                </div>
            </div>

            <!-- Card 4: Parameter Toleransi Server -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Parameter Server</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-lg font-bold text-slate-900 font-mono">
                        +{{ $tolerances['late'] }}m Masuk • +{{ $tolerances['overtime'] }}m Lembur
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Approver: <strong>{{ $stats['approvers_count'] }} User</strong></span>
                    <a href="{{ route('admin.settings.index') }}"
                        class="text-sky-600 font-semibold hover:underline">Setting &rarr;</a>
                </div>
            </div>

        </div>

        <!-- 2 Column Layout: Personil Terbaru & Log Presensi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Column 1: Karyawan Terdaftar Terbaru -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Personil Terdaftar Terbaru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Karyawan yang baru ditambahkan ke sistem.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                        class="text-xs font-semibold text-sky-600 hover:text-sky-700">Lihat Semua &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 font-sans">
                    @forelse($recentUsers as $user)
                        <div
                            class="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="truncate">
                                    <h4 class="font-bold text-slate-900 text-sm truncate">{{ $user->name }}</h4>
                                    <p class="text-xs text-slate-400 font-mono truncate">{{ $user->email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if ($user->rfid_uid)
                                    <span
                                        class="font-mono text-[11px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                        RFID: {{ $user->rfid_uid }}
                                    </span>
                                @else
                                    <span
                                        class="font-mono text-[11px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                        No RFID
                                    </span>
                                @endif

                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-sky-600 hover:bg-sky-50 transition"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada karyawan terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: Log Presensi Hari Ini -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Log Presensi Hari Ini</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Aktivitas check-in/checkout terbaru via RFID NodeMCU atau
                            Mobile.</p>
                    </div>
                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                        {{ now()->toDateString() }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100 font-sans">
                    @forelse($recentAttendances as $attendance)
                        <div
                            class="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-900 text-sm truncate">{{ $attendance->user->name ?? '—' }}
                                </h4>
                                <p class="text-xs text-slate-400 font-mono">
                                    In: {{ $attendance->time_in ?: '—' }} | Out: {{ $attendance->time_out ?: '—' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span
                                    class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $attendance->method ?: 'RFID' }}
                                </span>

                                @if ($attendance->is_late)
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[11px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                        Terlambat
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[11px] font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $attendance->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 text-xs">
                            <p class="font-medium text-slate-600">Belum ada aktivitas presensi hari ini</p>
                            <p class="text-[11px] text-slate-400 mt-1">Data akan masuk saat karyawan tap kartu RFID pada
                                NodeMCU.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- 2 Column Layout: Status Alat IoT & Pengajuan Cuti/Izin -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Column 1: Status & Maintenance Alat IoT -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Status Perangkat IoT NodeMCU</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Monitoring ketersediaan hardware & mode operasional alat.
                        </p>
                    </div>
                    <a href="{{ route('admin.devices.index') }}"
                        class="text-xs font-semibold text-sky-600 hover:text-sky-700">Pusat Maintenance &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 font-sans">
                    @forelse($devicesList as $device)
                        <div
                            class="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-900 text-sm truncate">{{ $device->name }}</h4>
                                    <span
                                        class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $device->device_uid }}</span>
                                </div>
                                <p class="text-xs text-slate-400 font-mono truncate">
                                    {{ $device->location }} • v{{ $device->firmware_version }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2.5 shrink-0">
                                @if ($device->status === 'MAINTENANCE')
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[11px] font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200">
                                        MAINTENANCE
                                    </span>
                                @elseif($device->isOnline())
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[11px] font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ONLINE
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[11px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                        OFFLINE
                                    </span>
                                @endif

                                <span
                                    class="font-mono text-[10px] font-bold px-2 py-0.5 rounded border {{ $device->mode === 'REGISTRATION' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-sky-50 text-sky-700 border-sky-200' }}">
                                    {{ $device->mode }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada perangkat IoT terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: Pengajuan Cuti / Izin Terbaru -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Pengajuan Cuti / Izin Karyawan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar permohonan yang perlu persetujuan atau baru
                            diperbarui.</p>
                    </div>
                    <a href="{{ route('approval.index') }}"
                        class="text-xs font-semibold text-sky-600 hover:text-sky-700">Lihat Semua
                        ({{ $stats['pending_leaves_count'] }} Pending) &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 font-sans">
                    @forelse($recentLeaves as $leave)
                        <div
                            class="p-4 sm:px-6 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                            <div class="space-y-1 min-w-0">
                                <h4 class="font-bold text-slate-900 text-sm truncate">{{ $leave->user->name ?? '—' }}</h4>
                                <p class="text-xs text-slate-400 font-mono truncate">
                                    {{ $leave->type }}: {{ $leave->start_date }} s/d {{ $leave->end_date }}
                                </p>
                                @if ($leave->reason)
                                    <p class="text-[11px] text-slate-500 italic truncate">"{{ $leave->reason }}"</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if ($leave->status === 'PENDING')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200">
                                        PENDING
                                    </span>
                                    <a href="{{ route('approval.index') }}"
                                        class="text-xs font-bold text-sky-600 hover:underline">Review</a>
                                @elseif($leave->status === 'APPROVED')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        APPROVED
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                        REJECTED
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada pengajuan cuti/izin.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
