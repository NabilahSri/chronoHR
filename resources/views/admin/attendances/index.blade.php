@extends('layouts.app')

@section('title', 'Log Presensi Karyawan - ChronoHR')
@section('page_title', 'Log Presensi Karyawan')
@section('page_subtitle', 'Pantau rekap riwayat presensi harian, jam masuk/pulang, status keterlambatan, dan lembur personil.')

@section('content')
    <div class="space-y-8">

        <!-- 4 Metric Cards Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Log -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Log Presensi</span>
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-mono font-bold text-xs">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Semua rekaman presensi sistem</p>
            </div>

            <!-- Card 2: Hadir di Kantor -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Hadir di Kantor</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-emerald-600 font-mono">{{ $stats['onsite'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Pindai kartu RFID / Presensi Kantor</p>
            </div>

            <!-- Card 3: WFA -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Kerja Jarak Jauh (WFA)</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-sky-600 font-mono">{{ $stats['wfa'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Persetujuan kerja jarak jauh</p>
            </div>

            <!-- Card 4: Terlambat -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Terlambat Masuk</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-rose-600 font-mono">{{ $stats['late'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Lewat dari toleransi masuk kantor</p>
            </div>
        </div>

        <!-- Office Geofence Reference Information & Actions -->
        <div class="bg-gradient-to-r from-sky-500/10 via-slate-50 to-indigo-500/10 rounded-3xl border border-sky-200/80 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-bold text-slate-900 tracking-tight">{{ $officeSettings['name'] ?? 'Kantor Pusat ChronoHR' }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-sky-100 text-sky-800 border border-sky-300">
                            Radius Kantor: {{ $officeSettings['radius'] ?? 100 }} Meter
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Acuan koordinat ({{ $officeSettings['latitude'] ?? '-6.2088' }}, {{ $officeSettings['longitude'] ?? '106.8456' }}): wajib untuk <strong>Magang</strong>, <strong>Karyawan WFA</strong>, dan karyawan kantor yang <strong>lupa membawa kartu RFID</strong>.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 flex-wrap">
                <button type="button" onclick="openManualOverrideModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 rounded-xl text-xs font-bold shadow-2xs transition cursor-pointer active:scale-95">
                    <span>+ Koreksi / Input Manual</span>
                </button>
                <button type="button" onclick="openLocationCheckInModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Presensi Lokasi (GPS)</span>
                </button>
                <a href="{{ route('admin.settings.index') }}"
                    class="p-2.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold transition"
                    title="Atur Titik Koordinat Kantor">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Kartu Filter Log Presensi -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Filter Log Presensi Harian</h3>
                        <p class="text-xs text-slate-600">Saring catatan presensi berdasarkan tanggal, status kehadiran, ketepatan waktu, atau nama personil.</p>
                    </div>
                </div>
                @if(request()->hasAny(['date', 'user_id', 'status', 'is_late', 'search']))
                    <a href="{{ route('admin.attendances.index') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('admin.attendances.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <!-- Tanggal Presensi -->
                    <div>
                        <label for="att_filter_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Presensi
                        </label>
                        <input type="date" id="att_filter_date" name="date" value="{{ $today }}"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                    </div>

                    <!-- Personil Karyawan (Searchable Dropdown) -->
                    <div>
                        <label for="att_filter_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="att_filter_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Personil</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Kehadiran -->
                    <div>
                        <label for="att_filter_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Kehadiran
                        </label>
                        <select id="att_filter_status" name="status"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Status</option>
                            <option value="HADIR_ON_SITE" {{ ($status ?? '') === 'HADIR_ON_SITE' ? 'selected' : '' }}>Hadir di Kantor</option>
                            <option value="WFA" {{ ($status ?? '') === 'WFA' ? 'selected' : '' }}>WFA (Jarak Jauh)</option>
                            <option value="SAKIT" {{ ($status ?? '') === 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                            <option value="IZIN" {{ ($status ?? '') === 'IZIN' ? 'selected' : '' }}>Izin</option>
                            <option value="ALPHA" {{ ($status ?? '') === 'ALPHA' ? 'selected' : '' }}>Alpa / Tanpa Keterangan</option>
                        </select>
                    </div>

                    <!-- Ketepatan Waktu -->
                    <div>
                        <label for="att_filter_late" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ketepatan Waktu
                        </label>
                        <select id="att_filter_late" name="is_late"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Ketepatan</option>
                            <option value="0" {{ ($isLate ?? '') === '0' ? 'selected' : '' }}>Tepat Waktu</option>
                            <option value="1" {{ ($isLate ?? '') === '1' ? 'selected' : '' }}>Terlambat Masuk</option>
                        </select>
                    </div>

                    <!-- Tombol Terapkan -->
                    <div>
                        <button type="submit"
                            class="w-full h-[42px] px-5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-sky-600">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Attendance Logs Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Data Log Presensi Karyawan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan data presensi tanggal: <strong class="text-slate-700 font-mono">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200 shrink-0 self-start sm:self-auto">
                    Total: {{ $attendances->total() }} Catatan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-4">Karyawan</th>
                            <th scope="col" class="px-6 py-4">Tanggal Presensi</th>
                            <th scope="col" class="px-6 py-4">Jam Masuk</th>
                            <th scope="col" class="px-6 py-4">Jam Pulang</th>
                            <th scope="col" class="px-6 py-4">Status & Metode</th>
                            <th scope="col" class="px-6 py-4">Ketepatan</th>
                            <th scope="col" class="px-6 py-4 text-right">Lembur</th>
                            <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Karyawan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-mono font-bold text-xs shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($att->user->name ?? 'K', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 text-sm leading-tight truncate">
                                                {{ $att->user->name ?? '-' }}
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[11px] text-slate-400 font-mono truncate">
                                                    {{ $att->user->email ?? '-' }}
                                                </span>
                                                <span class="text-[10px] font-bold font-mono px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ ($att->user->employment_type ?? '') === 'INTERN' ? 'MAGANG' : ($att->user->employment_type ?? 'STAF') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs">
                                        <p class="font-semibold text-slate-900">
                                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}
                                        </p>
                                        <p class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($att->date)->format('Y-m-d') }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Jam Masuk -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($att->time_in)
                                        <div class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-slate-800 bg-slate-100/80 px-2.5 py-1 rounded-lg border border-slate-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            <span>{{ substr($att->time_in, 0, 5) }}</span>
                                            <span class="text-[9px] text-slate-400">WIB</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-mono italic">-</span>
                                    @endif
                                </td>

                                <!-- Jam Pulang -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($att->time_out)
                                        <div class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-slate-800 bg-slate-100/80 px-2.5 py-1 rounded-lg border border-slate-200">
                                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>{{ substr($att->time_out, 0, 5) }}</span>
                                            <span class="text-[9px] text-slate-400">WIB</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-mono italic">Belum Pulang</span>
                                    @endif
                                </td>

                                <!-- Status & Metode -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        @if ($att->status === 'HADIR_ON_SITE')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                HADIR KANTOR
                                            </span>
                                        @elseif($att->status === 'WFA')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-sky-50 text-sky-700 border border-sky-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                                JARAK JAUH (WFA)
                                            </span>
                                        @elseif($att->status === 'SAKIT')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                SAKIT
                                            </span>
                                        @elseif($att->status === 'IZIN')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                IZIN RESMI
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-slate-100 text-slate-700 border border-slate-300 w-fit">
                                                ALPA
                                            </span>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-mono flex flex-col gap-0.5">
                                            @if ($att->method === 'RFID')
                                                <span class="text-slate-600">Pemindai RFID</span>
                                            @elseif($att->method === 'FACIAL_LOCATION')
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200 w-fit">
                                                    GPS & Lokasi
                                                </span>
                                                @if($att->latitude && $att->longitude)
                                                    <a href="https://www.google.com/maps?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank"
                                                        class="text-[9px] font-mono text-slate-400 hover:text-sky-600 transition truncate max-w-[150px] flex items-center gap-1"
                                                        title="Buka titik koordinat di Google Maps">
                                                        <span>{{ round($att->latitude, 4) }}, {{ round($att->longitude, 4) }}</span>
                                                    </a>
                                                @endif
                                            @elseif($att->method === 'FORM')
                                                <span>Formulir Izin/Cuti</span>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Ketepatan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($att->is_late)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Terlambat
                                        </span>
                                    @elseif($att->status === 'HADIR_ON_SITE' || $att->status === 'WFA')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Tepat Waktu
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-mono">-</span>
                                    @endif
                                </td>

                                <!-- Lembur -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if ($att->overtime_hours > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200">
                                            +{{ $att->overtime_hours }} Jam
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-mono">0</span>
                                    @endif
                                </td>

                                <!-- Aksi Koreksi Manual -->
                                @php
                                    $attEditPayload = [
                                        'userId' => $att->user_id,
                                        'date' => \Carbon\Carbon::parse($att->date)->format('Y-m-d'),
                                        'status' => $att->status,
                                        'method' => $att->method,
                                        'timeIn' => $att->time_in ? substr($att->time_in, 0, 5) : '',
                                        'timeOut' => $att->time_out ? substr($att->time_out, 0, 5) : '',
                                        'isLate' => $att->is_late ? '1' : '0',
                                        'overtimeHours' => (int) $att->overtime_hours,
                                    ];
                                @endphp
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button type="button"
                                        onclick='openManualOverrideModal(@json($attEditPayload))'
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 transition cursor-pointer">
                                        Koreksi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-slate-400 text-xs">
                                    <p class="font-medium text-slate-600">Tidak ada catatan log presensi yang ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan filter pencarian atau tanggal di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $attendances])
        </div>
    </div>

    <!-- MODAL PRESENSI LOKASI (GPS / Geofencing) -->
    <div id="locationCheckInModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden animate-slide-in-up">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">Presensi Berbasis Lokasi (GPS)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Validasi titik lokasi terhadap koordinat acuan kantor.</p>
                    </div>
                </div>
                <button type="button" onclick="closeLocationCheckInModal()"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form action="{{ route('attendances.location-check-in') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Pilih Karyawan -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">
                        Pilih Personil / Karyawan
                    </label>
                    <select name="user_id" id="modal_user_id" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ auth()->id() === $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }}) - {{ $u->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipe Presensi -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">
                        Opsi Kehadiran
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-3 rounded-2xl border border-slate-200 cursor-pointer hover:border-sky-400 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/50 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-slate-900">Hadir di Kantor</span>
                                <input type="radio" name="presence_type" value="ON_SITE" checked class="text-sky-600 focus:ring-0">
                            </div>
                            <span class="text-[11px] text-slate-500 leading-tight">Di Kantor (Lupa RFID / Magang Kantor)</span>
                        </label>

                        <label class="relative flex flex-col p-3 rounded-2xl border border-slate-200 cursor-pointer hover:border-sky-400 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/50 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-slate-900">Jarak Jauh / WFA</span>
                                <input type="radio" name="presence_type" value="WFA" class="text-sky-600 focus:ring-0">
                            </div>
                            <span class="text-[11px] text-slate-500 leading-tight">Kerja dari Mana Saja (WFA)</span>
                        </label>
                    </div>
                </div>

                <!-- GPS Coordinate Quick Detect -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Titik Koordinat GPS</span>
                        <button type="button" onclick="detectModalGPS()"
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-xs font-bold transition cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Ambil GPS Sekarang</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Latitude</label>
                            <input type="number" step="any" name="latitude" id="modal_latitude" required
                                value="{{ $officeSettings['latitude'] ?? '-6.20880000' }}"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold focus:outline-none focus:border-slate-900 transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Longitude</label>
                            <input type="number" step="any" name="longitude" id="modal_longitude" required
                                value="{{ $officeSettings['longitude'] ?? '106.84560000' }}"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold focus:outline-none focus:border-slate-900 transition">
                        </div>
                    </div>

                    <!-- Realtime Distance Badge -->
                    <div id="modal_distance_indicator" class="p-2.5 rounded-xl bg-sky-50 border border-sky-100 text-xs flex items-center justify-between text-sky-800">
                        <span class="font-medium">Jarak ke Titik Kantor:</span>
                        <span id="modal_distance_val" class="font-bold font-mono">0 Meter (Dalam Radius)</span>
                    </div>
                </div>

                <!-- Catatan / Keterangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">
                        Catatan (Opsional)
                    </label>
                    <input type="text" name="notes" placeholder="Contoh: Lupa membawa kartu RFID / Magang di Kantor"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs focus:outline-none focus:bg-white focus:border-slate-900 transition">
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeLocationCheckInModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                        Konfirmasi Presensi Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KOREKSI / INPUT PRESENSI MANUAL OLEH SUPER ADMIN -->
    <div id="manualOverrideModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Koreksi / Input Presensi Manual</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Digunakan apabila alat RFID mati lampu, lupa tap pulang, atau koreksi HRD.</p>
                </div>
                <button type="button" onclick="closeManualOverrideModal()"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.attendances.override') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Personil / Karyawan</label>
                    <select name="user_id" id="override_user_id" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Tanggal Presensi</label>
                        <input type="date" name="date" id="override_date" value="{{ $today }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                        <select name="status" id="override_status" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="HADIR_ON_SITE">Hadir di Kantor (On-Site)</option>
                            <option value="WFA">Kerja Jarak Jauh (WFA)</option>
                            <option value="SAKIT">Sakit</option>
                            <option value="IZIN">Izin Resmi</option>
                            <option value="ALPHA">Alpa / Tanpa Keterangan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Jam Masuk (WIB)</label>
                        <input type="time" name="time_in" id="override_time_in" value="08:00"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Jam Pulang (WIB)</label>
                        <input type="time" name="time_out" id="override_time_out" value="17:00"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Status Ketepatan</label>
                        <select name="is_late" id="override_is_late"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="0">Tepat Waktu</option>
                            <option value="1">Terlambat Masuk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-1.5">Jam Lembur (Jam)</label>
                        <input type="number" name="overtime_hours" id="override_overtime_hours" min="0" max="24" value="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeManualOverrideModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                        Simpan Koreksi Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Modal & Distance Calc -->
    <script>
        const officeLat = {{ (float) ($officeSettings['latitude'] ?? -6.2088) }};
        const officeLon = {{ (float) ($officeSettings['longitude'] ?? 106.8456) }};
        const officeRadius = {{ (int) ($officeSettings['radius'] ?? 100) }};

        function openLocationCheckInModal() {
            document.getElementById('locationCheckInModal').classList.remove('hidden');
            recalculateDistance();
        }

        function closeLocationCheckInModal() {
            document.getElementById('locationCheckInModal').classList.add('hidden');
        }

        function openManualOverrideModal(data = null) {
            if (data) {
                const userSelect = document.getElementById('override_user_id');
                if (userSelect) {
                    userSelect.value = data.userId;
                    userSelect.dispatchEvent(new Event('change'));
                }
                document.getElementById('override_date').value = data.date || '{{ $today }}';
                document.getElementById('override_status').value = data.status || 'HADIR_ON_SITE';
                document.getElementById('override_time_in').value = data.timeIn || '';
                document.getElementById('override_time_out').value = data.timeOut || '';
                document.getElementById('override_is_late').value = data.isLate || '0';
                document.getElementById('override_overtime_hours').value = data.overtimeHours || 0;
            }
            document.getElementById('manualOverrideModal').classList.remove('hidden');
        }

        function closeManualOverrideModal() {
            document.getElementById('manualOverrideModal').classList.add('hidden');
        }

        function calculateHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c);
        }

        function recalculateDistance() {
            const latInput = document.getElementById('modal_latitude');
            const lonInput = document.getElementById('modal_longitude');
            const distVal = document.getElementById('modal_distance_val');
            const distBox = document.getElementById('modal_distance_indicator');

            if (!latInput || !lonInput || !distVal || !distBox) return;

            const lat = parseFloat(latInput.value);
            const lon = parseFloat(lonInput.value);

            if (isNaN(lat) || isNaN(lon)) return;

            const dist = calculateHaversine(lat, lon, officeLat, officeLon);

            if (dist <= officeRadius) {
                distBox.className = 'p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between text-emerald-800';
                distVal.textContent = `${dist} Meter (✅ Di dalam radius ${officeRadius}m)`;
            } else {
                distBox.className = 'p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs flex items-center justify-between text-amber-800';
                distVal.textContent = `${dist} Meter (⚠️ Di luar radius kantor ${officeRadius}m)`;
            }
        }

        function detectModalGPS() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi GPS.');
                return;
            }

            const distVal = document.getElementById('modal_distance_val');
            if (distVal) distVal.textContent = 'Mendeteksi koordinat perangkat...';

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const lat = pos.coords.latitude.toFixed(8);
                    const lon = pos.coords.longitude.toFixed(8);
                    document.getElementById('modal_latitude').value = lat;
                    document.getElementById('modal_longitude').value = lon;
                    recalculateDistance();
                },
                function(err) {
                    alert('Gagal mendeteksi lokasi: ' + err.message);
                    recalculateDistance();
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('modal_latitude');
            const lonInput = document.getElementById('modal_longitude');
            if (latInput) latInput.addEventListener('input', recalculateDistance);
            if (lonInput) lonInput.addEventListener('input', recalculateDistance);
        });
    </script>
@endsection

