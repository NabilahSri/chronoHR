@extends('layouts.app')

@section('title', 'Laporan Presensi — ChronoHR')
@section('page_title', 'Laporan Rekapitulasi Presensi')
@section('page_subtitle', 'Analisis komprehensif kehadiran on-site RFID, WFH, keterlambatan, dan jam lembur personil.')

@section('content')
    <div class="w-full space-y-6">

        <!-- 5 KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 print:grid-cols-6">
            <!-- Total Log -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Log</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">{{ $stats['total'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Presensi tercatat</span>
            </div>

            <!-- Hadir On-Site -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">On-Site (Kantor)</span>
                <div class="text-2xl font-extrabold text-emerald-600 font-mono mt-2">{{ $stats['on_site'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Tap kartu RFID</span>
            </div>

            <!-- WFH -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600">WFH (Remote)</span>
                <div class="text-2xl font-extrabold text-sky-600 font-mono mt-2">{{ $stats['wfh'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Presensi mandiri</span>
            </div>

            <!-- Sakit & Izin -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Sakit / Izin</span>
                <div class="text-2xl font-extrabold text-indigo-600 font-mono mt-2">{{ $stats['sakit_izin'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Disetujui atasan</span>
            </div>

            <!-- Terlambat -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Terlambat</span>
                <div class="text-2xl font-extrabold text-rose-600 font-mono mt-2">{{ $stats['late'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Check-in lewat batas</span>
            </div>

            <!-- Jam Lembur -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Total Lembur</span>
                <div class="text-2xl font-extrabold text-amber-600 font-mono mt-2">{{ $stats['total_overtime'] }}<span
                        class="text-xs font-normal text-slate-500"> jam</span></div>
                <span class="text-[10px] text-slate-500 mt-1">Akumulasi lembur</span>
            </div>
        </div>

        <!-- Filter Bar Card (Hidden when printing) -->
        <div
            class="bg-white/95 rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs backdrop-blur-xs w-full print:hidden">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Parameter Filter Rekapitulasi Presensi</h3>
                </div>
                @if (request()->hasAny(['start_date', 'end_date', 'user_id', 'status', 'is_late']))
                    <a href="{{ route('reports.attendances') }}"
                        class="text-xs text-sky-600 hover:text-sky-700 font-semibold transition">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.attendances') }}" class="w-full space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Dari Tanggal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dari
                            Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sampai
                            Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                    </div>

                    <!-- Filter Personil -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personil
                            Karyawan</label>
                        <select name="user_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Personil —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Kehadiran -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status
                            Kehadiran</label>
                        <select name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Status —</option>
                            <option value="HADIR_ON_SITE" {{ $status === 'HADIR_ON_SITE' ? 'selected' : '' }}>Hadir On-Site
                                (Kantor)</option>
                            <option value="WFH" {{ $status === 'WFH' ? 'selected' : '' }}>WFH (Remote)</option>
                            <option value="SAKIT" {{ $status === 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                            <option value="IZIN" {{ $status === 'IZIN' ? 'selected' : '' }}>Izin</option>
                            <option value="ALPHA" {{ $status === 'ALPHA' ? 'selected' : '' }}>Alpha / Tanpa Keterangan
                            </option>
                        </select>
                    </div>

                    <!-- Keterlambatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ketepatan
                            Waktu</label>
                        <select name="is_late"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua —</option>
                            <option value="0" {{ $isLate === '0' ? 'selected' : '' }}>Tepat Waktu (On-Time)</option>
                            <option value="1" {{ $isLate === '1' ? 'selected' : '' }}>Terlambat (Late)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs w-full">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Log Presensi Karyawan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Periode: <strong
                            class="text-slate-700">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong>
                        s/d <strong
                            class="text-slate-700">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('reports.attendances.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-xs font-semibold shadow-2xs hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export ke Excel</span>
                    </a>
                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ $attendances->total() }} Data
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Karyawan & Status</th>
                            <th class="px-6 py-4">Tanggal Presensi</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Pulang</th>
                            <th class="px-6 py-4">Status & Metode</th>
                            <th class="px-6 py-4 text-center">Ketepatan</th>
                            <th class="px-6 py-4 text-right">Lembur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Karyawan -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $att->user->name ?? 'User Terhapus' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono flex items-center gap-2 mt-0.5">
                                        <span>{{ $att->user->email ?? '-' }}</span>
                                        @if (isset($att->user->employment_type))
                                            <span
                                                class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $att->user->employment_type }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4 font-mono text-xs text-slate-700">
                                    <div>{{ $att->date->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $att->date->translatedFormat('l') }}</div>
                                </td>

                                <!-- Jam Masuk -->
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if ($att->time_in)
                                        <span class="font-bold text-slate-900">{{ substr($att->time_in, 0, 5) }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <!-- Jam Pulang -->
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if ($att->time_out)
                                        <span class="font-bold text-slate-900">{{ substr($att->time_out, 0, 5) }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <!-- Status & Metode -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if ($att->status === 'HADIR_ON_SITE')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Hadir Kantor
                                            </span>
                                        @elseif($att->status === 'WFH')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                WFH (Remote)
                                            </span>
                                        @elseif($att->status === 'SAKIT')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sakit
                                            </span>
                                        @elseif($att->status === 'IZIN')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Izin
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Alpha
                                            </span>
                                        @endif

                                        @if ($att->method)
                                            <span
                                                class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                                {{ $att->method }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Ketepatan Waktu -->
                                <td class="px-6 py-4 text-center">
                                    @if ($att->is_late)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Terlambat
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Tepat Waktu
                                        </span>
                                    @endif
                                </td>

                                <!-- Jam Lembur -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    @if ($att->overtime_hours > 0)
                                        <span
                                            class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                            +{{ $att->overtime_hours }} Jam
                                        </span>
                                    @else
                                        <span class="text-slate-300">0 Jam</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada catatan presensi yang cocok dengan kriteria filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
