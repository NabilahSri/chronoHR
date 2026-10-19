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

                                        <span class="text-[10px] text-slate-400 font-mono">
                                            @if ($att->method === 'RFID')
                                                📡 RFID Scanner
                                            @elseif($att->method === 'FACIAL_LOCATION')
                                                📸 Face & GPS
                                            @elseif($att->method === 'FORM')
                                                📝 Form Izin/Cuti
                                            @else
                                                —
                                            @endif
                                        </span>
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
@endsection
