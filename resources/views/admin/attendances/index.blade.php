@extends('layouts.app')

@section('title', 'Log Presensi Karyawan — ChronoHR')
@section('page_title', 'Log Presensi Karyawan')
@section('page_subtitle', 'Pantau rekap riwayat presensi harian, jam tap in/out, status keterlambatan, dan lembur personil.')

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

            <!-- Card 2: On-Site -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Hadir On-Site</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-emerald-600 font-mono">{{ $stats['onsite'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Tap kartu RFID / Presensi Kantor</p>
            </div>

            <!-- Card 3: WFH -->
            <div class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Work From Home</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="text-3xl font-extrabold text-sky-600 font-mono">{{ $stats['wfh'] }}</div>
                <p class="text-xs text-slate-500 mt-2">Persetujuan kerja remote</p>
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
                            Geofence: {{ $officeSettings['radius'] ?? 100 }} Meter
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Acuan koordinat ({{ $officeSettings['latitude'] ?? '-6.2088' }}, {{ $officeSettings['longitude'] ?? '106.8456' }}): wajib untuk <strong>Magang</strong>, <strong>Karyawan WFH</strong>, dan karyawan on-site yang <strong>lupa membawa kartu RFID</strong>.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
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

        <!-- Search Bar Only (Log Presensi Per Hari) -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-5 shadow-xs backdrop-blur-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="flex-1 max-w-xl flex items-center gap-2.5">
                <div class="relative w-full group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama karyawan..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer shrink-0">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('admin.attendances.index') }}"
                        class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition text-center shrink-0 cursor-pointer">
                        Reset
                    </a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium shrink-0 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Log Presensi Hari Ini: <strong class="text-slate-800 font-mono">{{ now()->translatedFormat('l, d F Y') }}</strong></span>
            </div>
        </div>

        <!-- Attendance Logs Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Data Log Presensi Hari Ini</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Menampilkan data presensi hari ini: <strong class="text-slate-700 font-mono">{{ now()->translatedFormat('l, d F Y') }}</strong>
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200 shrink-0 self-start sm:self-auto">
                    Total: {{ $attendances->total() }} Record
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-4">Karyawan</th>
                            <th scope="col" class="px-6 py-4">Tanggal Presensi</th>
                            <th scope="col" class="px-6 py-4">Jam Masuk (Tap In)</th>
                            <th scope="col" class="px-6 py-4">Jam Pulang (Tap Out)</th>
                            <th scope="col" class="px-6 py-4">Status & Metode</th>
                            <th scope="col" class="px-6 py-4">Ketepatan</th>
                            <th scope="col" class="px-6 py-4 text-right">Lembur</th>
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
                                                {{ $att->user->name ?? '—' }}
                                            </p>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[11px] text-slate-400 font-mono truncate">
                                                    {{ $att->user->email ?? '—' }}
                                                </span>
                                                <span class="text-[10px] font-bold font-mono px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                    {{ $att->user->employment_type ?? 'STAFF' }}
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
                                        <span class="text-xs text-slate-400 font-mono italic">—</span>
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
                                        <span class="text-xs text-slate-400 font-mono italic">Belum Tap Out</span>
                                    @endif
                                </td>

                                <!-- Status & Metode -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        @if ($att->status === 'HADIR_ON_SITE')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                HADIR ON-SITE
                                            </span>
                                        @elseif($att->status === 'WFH')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-sky-50 text-sky-700 border border-sky-200 w-fit">
                                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                                REMOTE (WFH)
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
                                                ALPHA
                                            </span>
                                        @endif

                                        <div class="text-[10px] text-slate-400 font-mono flex flex-col gap-0.5">
                                            @if ($att->method === 'RFID')
                                                <span class="text-slate-600">📡 RFID Scanner</span>
                                            @elseif($att->method === 'FACIAL_LOCATION')
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200 w-fit">
                                                    📸 GPS & Lokasi
                                                </span>
                                                @if($att->latitude && $att->longitude)
                                                    <a href="https://www.google.com/maps?q={{ $att->latitude }},{{ $att->longitude }}" target="_blank"
                                                        class="text-[9px] font-mono text-slate-400 hover:text-sky-600 transition truncate max-w-[150px] flex items-center gap-1"
                                                        title="Buka titik koordinat di Google Maps">
                                                        <span>📍 {{ round($att->latitude, 4) }}, {{ round($att->longitude, 4) }}</span>
                                                    </a>
                                                @endif
                                            @elseif($att->method === 'FORM')
                                                <span>📝 Form Izin/Cuti</span>
                                            @else
                                                <span>—</span>
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
                                    @elseif($att->status === 'HADIR_ON_SITE' || $att->status === 'WFH')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Tepat Waktu
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-mono">—</span>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-12 text-center text-slate-400 text-xs">
                                    <p class="font-medium text-slate-600">Tidak ada catatan log presensi yang ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan filter pencarian atau tanggal di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $attendances->links() }}
                </div>
            @endif
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
                                {{ $u->name }} ({{ $u->employment_type }}) — {{ $u->email }}
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
                                <span class="text-xs font-bold text-slate-900">Hadir On-Site</span>
                                <input type="radio" name="presence_type" value="ON_SITE" checked class="text-sky-600 focus:ring-0">
                            </div>
                            <span class="text-[11px] text-slate-500 leading-tight">Di Kantor (Lupa RFID / Magang On-Site)</span>
                        </label>

                        <label class="relative flex flex-col p-3 rounded-2xl border border-slate-200 cursor-pointer hover:border-sky-400 has-[:checked]:border-sky-600 has-[:checked]:bg-sky-50/50 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-slate-900">Remote / WFH</span>
                                <input type="radio" name="presence_type" value="WFH" class="text-sky-600 focus:ring-0">
                            </div>
                            <span class="text-[11px] text-slate-500 leading-tight">Bekerja dari Rumah (WFH)</span>
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
                    <input type="text" name="notes" placeholder="Contoh: Lupa membawa kartu RFID / Magang On-Site"
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

