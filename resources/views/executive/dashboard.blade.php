@extends('layouts.app')

@section('title', 'Dashboard Eksekutif — ChronoHR ')
@section('page_title', 'Ringkasan Eksekutif & SDM')

@section('content')
    <div class="space-y-8">

        <!-- KPI Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            <!-- Total Headcount Card -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Tenaga Kerja</span>
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-slate-900 font-mono">{{ $stats['total_employees'] }}</div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-mono">
                    <span
                        class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $stats['kartap_count'] }}
                        Kartap</span>
                    <span
                        class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $stats['pkwt_count'] }}
                        PKWT</span>
                    <span
                        class="text-sky-700 font-bold bg-sky-50 px-2 py-0.5 rounded border border-sky-200">{{ $stats['intern_count'] }}
                        Magang</span>
                </div>
            </div>

            <!-- Presensi On-site Hari Ini -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Hadir On-Site
                            (Kantor)</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-emerald-600 font-mono">{{ $stats['today_onsite'] }}</div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Terverifikasi via Tap RFID NodeMCU</span>
                </div>
            </div>

            <!-- Presensi WFH Hari Ini -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Work From Home (WFH)</span>
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-sky-700 font-mono">{{ $stats['today_wfh'] }}</div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>Presensi GPS & Facial Mobile App</span>
                </div>
            </div>

            <!-- Pengeluaran Gaji Bulan Ini -->
            <div
                class="bg-white/95 p-6 rounded-3xl border border-slate-200/90 shadow-xs backdrop-blur-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Beban Payroll
                            (Published)</span>
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-sky-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 font-mono">
                        Rp {{ number_format($stats['latest_payroll_expense'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 font-medium">
                    Periode Bulan {{ now()->translatedFormat('F Y') }}
                </div>
            </div>
        </div>

        <!-- Live Attendance Activity Feed -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Presensi Karyawan Hari Ini</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring real-time kedatangan karyawan di seluruh channel
                        (RFID & Mobile).</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span
                        class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                        {{ now()->toDateString() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/80 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Metode Absen</th>
                            <th class="px-6 py-4">Jam Masuk (Time-In)</th>
                            <th class="px-6 py-4">Jam Pulang (Time-Out)</th>
                            <th class="px-6 py-4">Status Kehadiran</th>
                            <th class="px-6 py-4">Jam Lembur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($recentAttendances as $attendance)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $attendance->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $attendance->user->email ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold">
                                    <span
                                        class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $attendance->method ?: 'RFID' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                    {{ $attendance->time_in ?: '—' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                    {{ $attendance->time_out ?: '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($attendance->is_late)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ $attendance->status }} (Terlambat)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $attendance->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                    {{ $attendance->overtime_hours > 0 ? $attendance->overtime_hours . ' Jam' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <p class="font-medium text-slate-600">Belum ada presensi tercatat hari ini</p>
                                    <p class="text-xs text-slate-400 mt-1">Data presensi RFID dan Mobile akan muncul secara
                                        otomatis.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
