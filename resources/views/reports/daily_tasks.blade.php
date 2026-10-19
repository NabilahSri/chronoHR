@extends('layouts.app')

@section('title', 'Monitoring Task Harian — ChronoHR')
@section('page_title', 'Monitoring Laporan Task Harian')
@section('page_subtitle',
    'Pantau rencana kerja pagi (Check-In Plan) dan evaluasi capaian sore (Check-Out Review)
    seluruh personil.')

@section('content')
    <div class="w-full space-y-6">

        <!-- 5 KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 print:grid-cols-5">
            <!-- Total Personil -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Personil</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">{{ $stats['total'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Personil wajib lapor</span>
            </div>

            <!-- Sudah Lapor Pagi -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Rencana Pagi Masuk</span>
                <div class="text-2xl font-extrabold text-sky-600 font-mono mt-2">{{ $stats['morning_submitted'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Submit to-do kerja pagi</span>
            </div>

            <!-- Laporan Lengkap (Pagi + Sore) -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Lengkap (Pagi & Sore)</span>
                <div class="text-2xl font-extrabold text-emerald-600 font-mono mt-2">{{ $stats['completed'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Evaluasi sore selesai</span>
            </div>

            <!-- Menunggu Laporan Sore -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Menunggu Sore</span>
                <div class="text-2xl font-extrabold text-amber-600 font-mono mt-2">{{ $stats['morning_only'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Pending update jam pulang</span>
            </div>

            <!-- Belum Lapor Sama Sekali -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Belum Lapor Task</span>
                <div class="text-2xl font-extrabold text-rose-600 font-mono mt-2">{{ $stats['not_submitted'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Perlu diingatkan atasan</span>
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
                    <h3 class="text-sm font-bold text-slate-900">Parameter Pemantauan Task Harian</h3>
                </div>
                @if (request()->hasAny(['date', 'status', 'user_id', 'search']))
                    <a href="{{ route('reports.daily-tasks') }}"
                        class="text-xs text-sky-600 hover:text-sky-700 font-semibold transition">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.daily-tasks') }}" class="w-full space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Tanggal Laporan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal
                            Laporan</label>
                        <input type="date" name="date" value="{{ $date }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                    </div>

                    <!-- Status Laporan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status
                            Laporan</label>
                        <select name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Status —</option>
                            <option value="COMPLETED" {{ $statusFilter === 'COMPLETED' ? 'selected' : '' }}>Lengkap (Pagi &
                                Sore)</option>
                            <option value="MORNING_ONLY" {{ $statusFilter === 'MORNING_ONLY' ? 'selected' : '' }}>Hanya Pagi
                                (Menunggu Sore)</option>
                            <option value="NOT_SUBMITTED" {{ $statusFilter === 'NOT_SUBMITTED' ? 'selected' : '' }}>Belum
                                Lapor Task</option>
                        </select>
                    </div>

                    <!-- Filter Personil Karyawan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Personil
                            Karyawan</label>
                        <select name="user_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Personil —</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->id }}" {{ $userId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cari Nama / Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cari Kata
                            Kunci</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Nama / email karyawan..."
                                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
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

        <!-- Task Monitoring Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs w-full">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Status & Log Task Kerja Karyawan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pemantauan Tanggal: <strong
                            class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</strong>
                        · Sistem 2 Laporan: Rencana Masuk (Pagi) & Evaluasi Progres (Sore)
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('reports.daily-tasks.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-xs font-semibold shadow-2xs hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export ke Excel</span>
                    </a>

                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ $users->total() }} Personil
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4 text-center">Status Laporan</th>
                            <th class="px-6 py-4" style="min-width: 260px;">Report Pagi (Rencana Task)</th>
                            <th class="px-6 py-4" style="min-width: 280px;">Report Sore (Evaluasi Pulang)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $u)
                            @php
                                $report = $u->dailyReports->first();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Karyawan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-slate-900 text-white font-mono font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs">{{ $u->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                                            <span
                                                class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $u->employment_type ?? 'STAFF' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Laporan Badge -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if (!$report)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Belum Lapor
                                        </span>
                                    @elseif ($report->status === 'COMPLETED')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Lengkap (Pagi & Sore)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu Sore (Baru Pagi)
                                        </span>
                                    @endif
                                </td>

                                <!-- Report Pagi (Check-in Work Plan) -->
                                <td class="px-6 py-4 text-xs">
                                    @if ($report)
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                    Submit: {{ substr($report->morning_submitted_at ?? '08:00', 0, 5) }}
                                                    WIB
                                                </span>
                                            </div>
                                            <div class="text-slate-700 text-xs font-normal line-clamp-2 leading-relaxed">
                                                {{ $report->morning_planned_tasks }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">— Belum ada rencana task pagi
                                            —</span>
                                    @endif
                                </td>

                                <!-- Report Sore (Check-out Review) -->
                                <td class="px-6 py-4 text-xs">
                                    @if ($report && $report->status === 'COMPLETED')
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Submit: {{ substr($report->evening_submitted_at ?? '17:00', 0, 5) }}
                                                    WIB
                                                </span>
                                            </div>
                                            <p class="text-slate-600 text-[11px] line-clamp-1 italic">
                                                {{ $report->evening_completed_tasks }}
                                            </p>
                                        </div>
                                    @elseif ($report && $report->status === 'MORNING_ONLY')
                                        <div class="flex items-center gap-1.5 text-amber-600 text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Menunggu update mendekati jam pulang</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">—</span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        @if ($report)
                                            <!-- Tombol Lihat Detail Modal -->
                                            <button type="button"
                                                onclick="openDetailModal({{ json_encode([
                                                    'name' => $u->name,
                                                    'email' => $u->email,
                                                    'role' => $u->employment_type ?? 'STAFF',
                                                    'date' => \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y'),
                                                    'status' => $report->status,
                                                    'morning_time' => $report->morning_submitted_at ? substr($report->morning_submitted_at, 0, 5) . ' WIB' : '-',
                                                    'morning_tasks' => $report->morning_planned_tasks,
                                                    'evening_time' => $report->evening_submitted_at
                                                        ? substr($report->evening_submitted_at, 0, 5) . ' WIB'
                                                        : 'Belum Submit',
                                                    'completed_tasks' => $report->evening_completed_tasks ?? 'Belum ada data',
                                                    'pending_tasks' => $report->evening_pending_tasks ?? 'Tidak ada kendala / pending',
                                                    'additional_tasks' => $report->evening_additional_tasks ?? 'Tidak ada task tambahan',
                                                ]) }})"
                                                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition cursor-pointer">
                                                Lihat Log Task
                                            </button>

                                            <!-- Tombol Update Sore Berbasis Checklist -->
                                            @if ($report->status === 'MORNING_ONLY')
                                                <button type="button"
                                                    onclick="openUpdateEveningModal('{{ route('reports.daily-tasks.update', $report->id) }}', '{{ $u->name }}', {{ json_encode($report->morning_planned_tasks) }}, {{ json_encode($report->evening_additional_tasks ?? '') }}, {{ json_encode($report->evening_completed_tasks ?? '') }})"
                                                    class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-2xs transition cursor-pointer flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                    </svg>
                                                    <span>Checklist Sore</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                    onclick="openUpdateEveningModal('{{ route('reports.daily-tasks.update', $report->id) }}', '{{ $u->name }}', {{ json_encode($report->morning_planned_tasks) }}, {{ json_encode($report->evening_additional_tasks ?? '') }}, {{ json_encode($report->evening_completed_tasks ?? '') }})"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition cursor-pointer"
                                                    title="Ubah Checklist Sore">
                                                    Edit
                                                </button>
                                            @endif
                                        @else
                                            <button type="button"
                                                onclick="openCreateModalForUser('{{ $u->id }}', '{{ $u->name }}', '{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}')"
                                                class="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 text-xs font-semibold transition cursor-pointer">
                                                Isi Laporan
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data personil atau laporan task yang cocok dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL 1: DETAIL TASK KOMPARASI PAGI & SORE -->
    <div id="detail-modal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-3xl w-full p-6 sm:p-8 space-y-6 animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="modal-user-name" class="text-base font-bold text-slate-900 leading-tight">Nama Karyawan
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            <span id="modal-user-email">email@test.com</span> · Tanggal: <strong id="modal-date"
                                class="text-slate-700">Tanggal</strong>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()"
                    class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content: 2 Sisi (Pagi vs Sore) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Kolom Kiri: Laporan Pagi (Check-in Work Plan) -->
                <div class="p-5 rounded-2xl bg-sky-50/40 border border-sky-100 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-sky-100/80">
                        <span class="text-xs font-bold text-sky-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>Report Pagi (Rencana Masuk)</span>
                        </span>
                        <span id="modal-morning-time"
                            class="font-mono text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-md">
                            08:00 WIB
                        </span>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Daftar Task
                            yang Direncanakan:</label>
                        <div id="modal-morning-tasks"
                            class="text-xs text-slate-700 whitespace-pre-line leading-relaxed font-sans bg-white p-3.5 rounded-xl border border-sky-100/80">
                            -
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Laporan Sore (Check-out Review) -->
                <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>Report Sore (Update Pulang)</span>
                        </span>
                        <span id="modal-evening-time"
                            class="font-mono text-[10px] font-bold text-slate-700 bg-slate-200 px-2 py-0.5 rounded-md">
                            17:00 WIB
                        </span>
                    </div>

                    <!-- Task Selesai -->
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1">✓ Task
                            yang Sudah Selesai:</label>
                        <div id="modal-completed-tasks"
                            class="text-xs text-slate-700 whitespace-pre-line leading-relaxed bg-white p-3 rounded-xl border border-emerald-100">
                            -
                        </div>
                    </div>

                    <!-- Task Belum Selesai (Pending) -->
                    <div>
                        <label class="block text-[10px] font-bold text-amber-700 uppercase tracking-wider mb-1"> Task
                            Belum Selesai / Pending:</label>
                        <div id="modal-pending-tasks"
                            class="text-xs text-slate-700 whitespace-pre-line leading-relaxed bg-white p-3 rounded-xl border border-amber-100">
                            -
                        </div>
                    </div>

                    <!-- Task Tambahan -->
                    <div>
                        <label class="block text-[10px] font-bold text-sky-700 uppercase tracking-wider mb-1">+ Task
                            Tambahan Hari Ini:</label>
                        <div id="modal-additional-tasks"
                            class="text-xs text-slate-700 whitespace-pre-line leading-relaxed bg-white p-3 rounded-xl border border-sky-100">
                            -
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeDetailModal()"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                    Tutup Tinjauan
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: INPUT LAPORAN TASK HARIAN (LANGSUNG UNTUK KARYAWAN & TANGGAL TERPILIH) -->
    <div id="create-modal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-5 animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Isi Laporan Task Kerja</h3>
                        <p class="text-xs text-slate-500">
                            Karyawan: <strong id="create-user-name" class="text-slate-800">-</strong>
                            · Tanggal: <strong id="create-date-display" class="text-slate-800 font-mono">{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</strong>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateModal()"
                    class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('reports.daily-tasks.store') }}" method="POST" id="create-task-form" class="space-y-4">
                @csrf
                <!-- Hidden Karyawan & Tanggal (Tanpa Perlu Memilih Lagi) -->
                <input type="hidden" name="user_id" id="create-user-id" value="">
                <input type="hidden" name="date" id="create-date" value="{{ $date }}">
                <input type="hidden" name="with_evening_checklist" id="create-with-evening" value="0">

                <!-- BAGIAN 1: RENCANA TASK PAGI (WAJIB) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="create-morning-tasks"
                            class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            📋 1. Rencana Task Pagi (Check-In) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                            Wajib Disubmit
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Ketik daftar task yang direncanakan untuk dikerjakan (pisahkan per baris):
                    </p>
                    <textarea name="morning_planned_tasks" id="create-morning-tasks" rows="3" required
                        placeholder="Contoh:&#10;1. Selesaikan bug report login RFID&#10;2. Pengujian modul slip gaji&#10;3. Koordinasi dengan tim finance"
                        oninput="syncMorningTasksToCreateChecklist()"
                        class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition leading-relaxed"></textarea>
                </div>

                <!-- BAGIAN 2: CHECKLIST SORE (OPSIONAL JIKA INGIN LANGSUNG DILENGKAPI) -->
                <div class="pt-3 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-100/70 transition"
                        onclick="toggleCreateEveningSection()">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0">
                                🌇
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">Langsung Lengkapi Evaluasi Sore Sekarang?</span>
                                <span class="text-[11px] text-slate-500">Centang task yang selesai sore ini jika ingin laporan langsung tuntas</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-600" id="create-evening-toggle-label">Buka Checklist</span>
                            <div id="create-evening-badge" class="w-5 h-5 rounded-md border border-slate-300 bg-white flex items-center justify-center text-emerald-600 text-xs font-bold">
                                
                            </div>
                        </div>
                    </div>

                    <!-- Container Checklist Sore -->
                    <div id="create-evening-container" class="hidden space-y-4 pt-1 animate-in fade-in duration-200">
                        <!-- Input Task Tambahan Terlebih Dahulu -->
                        <div class="p-4 bg-sky-50/60 rounded-2xl border border-sky-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="create-evening-additional-tasks"
                                    class="block text-xs font-bold text-sky-950 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>➕ Task Tambahan Hari Ini (Ketik Tambahan Dulu)</span>
                                </label>
                                <span class="text-[10px] font-semibold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">Opsional</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-normal">
                                Jika ada tugas baru atau dadakan di luar rencana pagi tadi, ketik di bawah ini terlebih dahulu:
                            </p>
                            <textarea id="create-evening-additional-tasks" name="evening_additional_tasks" rows="2"
                                placeholder="Contoh:&#10;- Perbaikan bug urgent di server&#10;- Koordinasi dadakan dengan tim finance"
                                class="w-full px-3.5 py-2.5 bg-white border border-sky-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition leading-relaxed"></textarea>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-[10px] text-slate-400">Ingin memasukkan task tambahan ke checklist di bawah?</span>
                                <button type="button" onclick="syncCreateAdditionalTasksToChecklist()"
                                    class="px-2.5 py-1 bg-white hover:bg-sky-100 text-sky-700 border border-sky-200 rounded-lg text-[11px] font-semibold transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>+ Masukkan ke Checklist</span>
                                </button>
                            </div>
                        </div>

                        <!-- Checklist Task Selesai -->
                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1">
                                <div>
                                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                        ☑️ Checklist Task yang Sudah Selesai
                                    </label>
                                    <p class="text-[11px] text-slate-500">
                                        Centang task yang <strong>sudah selesai</strong>. Task yang <strong>tidak dicentang</strong> otomatis tercatat sebagai <em>Belum Selesai (Pending)</em>.
                                    </p>
                                </div>

                                <!-- Tombol Cepat: Centang Semua / Reset -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" onclick="toggleAllCreateTaskCheckboxes(true)"
                                        class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition cursor-pointer">
                                        Centang Semua
                                    </button>
                                    <button type="button" onclick="toggleAllCreateTaskCheckboxes(false)"
                                        class="text-[11px] font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg border border-slate-200 transition cursor-pointer">
                                        Kosongkan
                                    </button>
                                </div>
                            </div>

                            <!-- Progress & Summary Bar -->
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span id="create-checklist-counter">0 dari 0 task selesai (0%)</span>
                                    </span>
                                    <span id="create-checklist-pending-counter" class="text-[11px] font-medium text-amber-700">
                                        0 pending
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                    <div id="create-checklist-progress-bar" class="bg-emerald-500 h-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            </div>

                            <!-- Dynamic Checklist Items Container -->
                            <div id="create-tasks-checklist-container" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                <!-- Dimuat otomatis dari textarea rencana pagi -->
                            </div>

                            <div id="create-empty-tasks-alert" class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                Ketik task rencana pagi terlebih dahulu pada kolom di atas agar muncul di daftar checklist ini.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span id="create-submit-button-text">Simpan Laporan Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: UPDATE SORE (CHECK-OUT REVIEW BERBASIS CHECKLIST) -->
    <div id="update-evening-modal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-5 animate-in fade-in zoom-in duration-150">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Checklist Evaluasi Sore (Jam Pulang)</h3>
                        <p class="text-xs text-slate-500">Karyawan: <strong id="update-user-name"
                                class="text-slate-800">-</strong></p>
                    </div>
                </div>
                <button type="button" onclick="closeUpdateEveningModal()"
                    class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="update-evening-form" action="" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- BAGIAN 1: INPUT TASK TAMBAHAN TERLEBIH DAHULU -->
                <div class="p-4 bg-sky-50/60 rounded-2xl border border-sky-100 space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="update-evening-additional-tasks"
                            class="block text-xs font-bold text-sky-950 uppercase tracking-wider flex items-center gap-1.5">
                            <span>➕ 1. Task Tambahan Hari Ini (Ketik Tambahan Dulu)</span>
                        </label>
                        <span
                            class="text-[10px] font-semibold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">Opsional</span>
                    </div>
                    <p class="text-[11px] text-slate-500 leading-normal">
                        Jika ada tugas dadakan di luar rencana pagi tadi, ketik di bawah ini terlebih dahulu:
                    </p>
                    <textarea id="update-evening-additional-tasks" name="evening_additional_tasks" rows="2"
                        placeholder="Contoh:&#10;- Perbaikan bug urgent di server&#10;- Koordinasi dadakan dengan tim finance"
                        class="w-full px-3.5 py-2.5 bg-white border border-sky-200 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 transition leading-relaxed"></textarea>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-[10px] text-slate-400">Ingin memasukkan task tambahan ke checklist di
                            bawah?</span>
                        <button type="button" onclick="syncAdditionalTasksToChecklist()"
                            class="px-2.5 py-1 bg-white hover:bg-sky-100 text-sky-700 border border-sky-200 rounded-lg text-[11px] font-semibold transition cursor-pointer flex items-center gap-1 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <span>+ Masukkan ke Checklist</span>
                        </button>
                    </div>
                </div>

                <!-- BAGIAN 2: CHECKLIST TASK SELESAI -->
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1">
                        <div>
                            <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                ☑️ 2. Checklist Task yang Sudah Selesai
                            </label>
                            <p class="text-[11px] text-slate-500">
                                Centang task yang <strong>sudah selesai</strong>. Task yang <strong>tidak dicentang</strong>
                                otomatis tercatat sebagai <em>Belum Selesai (Pending)</em>.
                            </p>
                        </div>

                        <!-- Tombol Cepat: Centang Semua / Reset -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="toggleAllTaskCheckboxes(true)"
                                class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition cursor-pointer">
                                Centang Semua
                            </button>
                            <button type="button" onclick="toggleAllTaskCheckboxes(false)"
                                class="text-[11px] font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg border border-slate-200 transition cursor-pointer">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <!-- Progress & Summary Bar -->
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span id="update-checklist-counter">0 dari 0 task selesai (0%)</span>
                            </span>
                            <span id="update-checklist-pending-counter" class="text-[11px] font-medium text-amber-700">
                                0 pending
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div id="update-checklist-progress-bar"
                                class="bg-emerald-500 h-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <!-- Container Checklist Dinamis -->
                    <div id="update-tasks-checklist-container" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                        <!-- Dimuat lewat Javascript -->
                    </div>

                    <!-- Fallback manual jika kosong -->
                    <div id="update-empty-tasks-alert"
                        class="hidden p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                        Belum ada daftar task pagi yang tercatat. Silakan ketik task langsung di task tambahan di atas.
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeUpdateEveningModal()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Evaluasi Sore</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT MODAL HANDLERS -->
    <script>
        // State checklist sore
        let currentMorningTasksList = [];
        let currentAdditionalTasksList = [];

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function parseTasksIntoList(text) {
            if (!text || typeof text !== 'string') return [];
            return text.split(/\r?\n/)
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .map(line => {
                    // Bersihkan numbering awalan seperti "1. ", "1) ", "- ", "* "
                    return line.replace(/^(\d+[\.\)]\s*|[-*•]\s*)/, '').trim();
                })
                .filter(line => line.length > 0);
        }

        function formatTaskHtml(text, type) {
            if (!text || text.trim() === '' || text.trim() === '-') {
                return '<span class="text-slate-400 italic text-xs">Tidak ada</span>';
            }
            const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
            if (lines.length === 0) {
                return '<span class="text-slate-400 italic text-xs">Tidak ada</span>';
            }

            let badgeClass = '';
            let icon = '';
            if (type === 'completed') {
                badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                icon = '✓';
            } else if (type === 'pending') {
                badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                icon = '';
            } else if (type === 'additional') {
                badgeClass = 'bg-sky-50 text-sky-800 border-sky-200';
                icon = '➕';
            } else {
                badgeClass = 'bg-slate-50 text-slate-800 border-slate-200';
                icon = '📌';
            }

            return '<div class="space-y-1.5">' + lines.map(line => {
                return `<div class="flex items-start gap-2 p-2 rounded-xl border text-xs leading-relaxed ${badgeClass}">
                    <span class="font-bold shrink-0 mt-0.5">${icon}</span>
                    <span class="font-medium">${escapeHtml(line)}</span>
                </div>`;
            }).join('') + '</div>';
        }

        function openDetailModal(data) {
            document.getElementById('modal-user-name').innerText = data.name;
            document.getElementById('modal-user-email').innerText = data.email;
            document.getElementById('modal-date').innerText = data.date;
            document.getElementById('modal-morning-time').innerText = data.morning_time;
            document.getElementById('modal-morning-tasks').innerHTML = formatTaskHtml(data.morning_tasks, 'morning');
            document.getElementById('modal-evening-time').innerText = data.evening_time;
            document.getElementById('modal-completed-tasks').innerHTML = formatTaskHtml(data.completed_tasks, 'completed');
            document.getElementById('modal-pending-tasks').innerHTML = formatTaskHtml(data.pending_tasks, 'pending');
            document.getElementById('modal-additional-tasks').innerHTML = formatTaskHtml(data.additional_tasks,
                'additional');
            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        // State untuk modal Input Laporan (Create Modal)
        let currentCreateMorningTasks = [];
        let currentCreateAdditionalTasks = [];

        function openCreateModalForUser(userId, userName, formattedDate) {
            document.getElementById('create-user-id').value = userId;
            document.getElementById('create-user-name').innerText = userName;
            if (formattedDate) {
                document.getElementById('create-date-display').innerText = formattedDate;
            }
            document.getElementById('create-morning-tasks').value = '';
            document.getElementById('create-evening-additional-tasks').value = '';

            // Reset toggle report sore
            document.getElementById('create-with-evening').value = '0';
            const eveningContainer = document.getElementById('create-evening-container');
            const toggleLabel = document.getElementById('create-evening-toggle-label');
            const badge = document.getElementById('create-evening-badge');
            const submitBtnText = document.getElementById('create-submit-button-text');

            if (eveningContainer) eveningContainer.classList.add('hidden');
            if (toggleLabel) toggleLabel.innerText = 'Buka Checklist';
            if (badge) {
                badge.innerText = '';
                badge.className = 'w-5 h-5 rounded-md border border-slate-300 bg-white flex items-center justify-center text-emerald-600 text-xs font-bold';
            }
            if (submitBtnText) submitBtnText.innerText = 'Simpan Laporan Task';

            currentCreateMorningTasks = [];
            currentCreateAdditionalTasks = [];
            renderCreateChecklist();

            document.getElementById('create-modal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('create-modal').classList.add('hidden');
        }

        function toggleCreateEveningSection() {
            const container = document.getElementById('create-evening-container');
            const hiddenInput = document.getElementById('create-with-evening');
            const label = document.getElementById('create-evening-toggle-label');
            const badge = document.getElementById('create-evening-badge');
            const submitBtnText = document.getElementById('create-submit-button-text');

            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                hiddenInput.value = '1';
                label.innerText = 'Tutup Checklist';
                badge.innerText = '✓';
                badge.className = 'w-5 h-5 rounded-md border border-emerald-400 bg-emerald-500 text-white flex items-center justify-center text-xs font-bold';
                if (submitBtnText) submitBtnText.innerText = 'Simpan Laporan Pagi & Evaluasi Sore';
                syncMorningTasksToCreateChecklist();
            } else {
                container.classList.add('hidden');
                hiddenInput.value = '0';
                label.innerText = 'Buka Checklist';
                badge.innerText = '';
                badge.className = 'w-5 h-5 rounded-md border border-slate-300 bg-white flex items-center justify-center text-emerald-600 text-xs font-bold';
                if (submitBtnText) submitBtnText.innerText = 'Simpan Laporan Task';
            }
        }

        function syncMorningTasksToCreateChecklist() {
            const text = document.getElementById('create-morning-tasks').value;
            currentCreateMorningTasks = parseTasksIntoList(text);
            renderCreateChecklist();
        }

        function renderCreateChecklist() {
            const container = document.getElementById('create-tasks-checklist-container');
            const emptyAlert = document.getElementById('create-empty-tasks-alert');
            if (!container) return;

            container.innerHTML = '';

            if (currentCreateMorningTasks.length === 0 && currentCreateAdditionalTasks.length === 0) {
                if (emptyAlert) emptyAlert.classList.remove('hidden');
                updateCreateChecklistCounter();
                return;
            }

            if (emptyAlert) emptyAlert.classList.add('hidden');

            // 1. Render Task Pagi
            currentCreateMorningTasks.forEach((task, idx) => {
                const card = document.createElement('label');
                card.id = `create-morning-task-card-${idx}`;
                card.className = 'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-slate-50/80 border-slate-200 hover:bg-slate-100/70';

                card.innerHTML = `
                    <input type="hidden" name="all_morning_tasks[]" value="${escapeHtml(task)}">
                    <input type="checkbox" name="completed_task_indices[]" value="${idx}"
                        class="task-item-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                        onchange="handleCreateTaskCheckboxChange(this, '${card.id}', false)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-800 task-label-text">
                                ${idx + 1}. ${escapeHtml(task)}
                            </span>
                            <span class="task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-amber-100 text-amber-800">
                                Belum Selesai
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 task-status-hint">
                            Tidak dicentang = otomatis tercatat sebagai Pending
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });

            // 2. Render Task Tambahan jika ada
            currentCreateAdditionalTasks.forEach((task, aIdx) => {
                const card = document.createElement('label');
                card.id = `create-additional-task-card-${aIdx}`;
                card.className = 'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/20';

                card.innerHTML = `
                    <input type="hidden" name="additional_tasks_checklist[]" value="${escapeHtml(task)}">
                    <input type="checkbox" name="completed_additional_indices[]" value="${aIdx}" checked
                        class="task-item-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                        onchange="handleCreateTaskCheckboxChange(this, '${card.id}', true)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-emerald-950 font-bold task-label-text">
                                <span class="text-sky-700 font-bold">[Tambahan]</span> ${escapeHtml(task)}
                            </span>
                            <span class="task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-emerald-100 text-emerald-800">
                                ✓ Selesai
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 task-status-hint">
                            Tercatat di daftar task selesai sore
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });

            updateCreateChecklistCounter();
        }

        function handleCreateTaskCheckboxChange(checkbox, cardId, isAdditional) {
            const card = document.getElementById(cardId);
            if (!card) return;

            const isChecked = checkbox.checked;
            const badge = card.querySelector('.task-status-badge');
            const labelText = card.querySelector('.task-label-text');
            const hint = card.querySelector('.task-status-hint');

            if (isChecked) {
                card.className = 'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/20';
                if (badge) {
                    badge.className = 'task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-emerald-100 text-emerald-800';
                    badge.innerText = '✓ Selesai';
                }
                if (labelText) labelText.classList.add('text-emerald-950', 'font-bold');
                if (hint) hint.innerText = 'Tercatat di daftar task selesai sore';
            } else {
                card.className = isAdditional
                    ? 'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-sky-50/40 border-sky-200 hover:bg-sky-50'
                    : 'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-slate-50/80 border-slate-200 hover:bg-slate-100/70';
                if (badge) {
                    badge.className = 'task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-amber-100 text-amber-800';
                    badge.innerText = 'Belum Selesai';
                }
                if (labelText) labelText.classList.remove('text-emerald-950');
                if (hint) hint.innerText = 'Tidak dicentang = otomatis tercatat sebagai Pending';
            }

            updateCreateChecklistCounter();
        }

        function syncCreateAdditionalTasksToChecklist() {
            const rawAdditional = document.getElementById('create-evening-additional-tasks').value;
            const lines = parseTasksIntoList(rawAdditional);

            if (lines.length === 0) {
                alert('Ketik terlebih dahulu task tambahan pada kotak di atas.');
                return;
            }

            currentCreateAdditionalTasks = lines;
            renderCreateChecklist();
        }

        function toggleAllCreateTaskCheckboxes(checkAll) {
            const checkboxes = document.querySelectorAll('#create-tasks-checklist-container .task-item-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkAll;
                const changeEvent = new Event('change', { bubbles: true });
                cb.dispatchEvent(changeEvent);
            });
        }

        function updateCreateChecklistCounter() {
            const checkboxes = document.querySelectorAll('#create-tasks-checklist-container .task-item-checkbox');
            const total = checkboxes.length;
            let completed = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) completed++;
            });

            const pending = total - completed;
            const percent = total > 0 ? Math.round((completed / total) * 100) : 0;

            const counter = document.getElementById('create-checklist-counter');
            const pendingCounter = document.getElementById('create-checklist-pending-counter');
            const bar = document.getElementById('create-checklist-progress-bar');

            if (counter) counter.innerText = `${completed} dari ${total} task selesai (${percent}%)`;
            if (pendingCounter) pendingCounter.innerText = `${pending} pending`;
            if (bar) bar.style.width = `${percent}%`;
        }

        function openUpdateEveningModal(actionUrl, userName, morningTasks, existingAdditional = '', existingCompleted =
            '') {
            document.getElementById('update-evening-form').action = actionUrl;
            document.getElementById('update-user-name').innerText = userName;
            document.getElementById('update-evening-additional-tasks').value = existingAdditional || '';

            currentMorningTasksList = parseTasksIntoList(morningTasks);
            currentAdditionalTasksList = [];

            // Jika dalam mode edit (sudah ada data completed sebelumnya)
            const completedLines = parseTasksIntoList(existingCompleted);

            renderChecklist(completedLines);
            document.getElementById('update-evening-modal').classList.remove('hidden');
        }

        function closeUpdateEveningModal() {
            document.getElementById('update-evening-modal').classList.add('hidden');
        }

        function renderChecklist(completedLines = []) {
            const container = document.getElementById('update-tasks-checklist-container');
            const emptyAlert = document.getElementById('update-empty-tasks-alert');
            container.innerHTML = '';

            if (currentMorningTasksList.length === 0 && currentAdditionalTasksList.length === 0) {
                emptyAlert.classList.remove('hidden');
                updateChecklistCounter();
                return;
            }

            emptyAlert.classList.add('hidden');

            // 1. Render Task Pagi
            currentMorningTasksList.forEach((task, idx) => {
                const isChecked = completedLines.length > 0 && completedLines.some(cLine =>
                    cLine.toLowerCase().includes(task.toLowerCase()) || task.toLowerCase().includes(cLine
                        .toLowerCase())
                );

                const card = document.createElement('label');
                card.id = `morning-task-card-${idx}`;
                card.className = `task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none ${
                    isChecked ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/20' : 'bg-slate-50/80 border-slate-200 hover:bg-slate-100/70'
                }`;

                card.innerHTML = `
                    <input type="hidden" name="all_morning_tasks[]" value="${escapeHtml(task)}">
                    <input type="checkbox" name="completed_task_indices[]" value="${idx}" ${isChecked ? 'checked' : ''}
                        class="task-item-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                        onchange="handleTaskCheckboxChange(this, '${card.id}', false)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold ${isChecked ? 'text-emerald-950 font-bold' : 'text-slate-800'} task-label-text">
                                ${idx + 1}. ${escapeHtml(task)}
                            </span>
                            <span class="task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 ${
                                isChecked ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                            }">
                                ${isChecked ? '✓ Selesai' : ' Belum Selesai'}
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 task-status-hint">
                            ${isChecked ? 'Tercatat di daftar task selesai sore' : 'Tidak dicentang = otomatis tercatat sebagai Pending'}
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });

            // 2. Render Task Tambahan jika dimasukkan ke checklist
            currentAdditionalTasksList.forEach((task, aIdx) => {
                const card = document.createElement('label');
                card.id = `additional-task-card-${aIdx}`;
                card.className =
                    'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/20';

                card.innerHTML = `
                    <input type="hidden" name="additional_tasks_checklist[]" value="${escapeHtml(task)}">
                    <input type="checkbox" name="completed_additional_indices[]" value="${aIdx}" checked
                        class="task-item-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                        onchange="handleTaskCheckboxChange(this, '${card.id}', true)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-emerald-950 font-bold task-label-text">
                                <span class="text-sky-700 font-bold">[Tambahan]</span> ${escapeHtml(task)}
                            </span>
                            <span class="task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-emerald-100 text-emerald-800">
                                ✓ Selesai
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5 task-status-hint">
                            Tercatat di daftar task selesai sore
                        </p>
                    </div>
                `;
                container.appendChild(card);
            });

            updateChecklistCounter();
        }

        function handleTaskCheckboxChange(checkbox, cardId, isAdditional) {
            const card = document.getElementById(cardId);
            if (!card) return;

            const isChecked = checkbox.checked;
            const badge = card.querySelector('.task-status-badge');
            const labelText = card.querySelector('.task-label-text');
            const hint = card.querySelector('.task-status-hint');

            if (isChecked) {
                card.className =
                    'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-400/20';
                if (badge) {
                    badge.className =
                        'task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-emerald-100 text-emerald-800';
                    badge.innerText = '✓ Selesai';
                }
                if (labelText) labelText.classList.add('text-emerald-950', 'font-bold');
                if (hint) hint.innerText = 'Tercatat di daftar task selesai sore';
            } else {
                card.className = isAdditional ?
                    'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-sky-50/40 border-sky-200 hover:bg-sky-50' :
                    'task-checklist-item flex items-start gap-3 p-3 rounded-2xl border transition cursor-pointer select-none bg-slate-50/80 border-slate-200 hover:bg-slate-100/70';
                if (badge) {
                    badge.className =
                        'task-status-badge text-[10px] font-bold px-2 py-0.5 rounded-md shrink-0 bg-amber-100 text-amber-800';
                    badge.innerText = ' Belum Selesai';
                }
                if (labelText) labelText.classList.remove('text-emerald-950');
                if (hint) hint.innerText = 'Tidak dicentang = otomatis tercatat sebagai Pending';
            }

            updateChecklistCounter();
        }

        function syncAdditionalTasksToChecklist() {
            const rawAdditional = document.getElementById('update-evening-additional-tasks').value;
            const lines = parseTasksIntoList(rawAdditional);

            if (lines.length === 0) {
                alert('Ketik terlebih dahulu task tambahan pada kotak di atas.');
                return;
            }

            currentAdditionalTasksList = lines;
            renderChecklist();
        }

        function toggleAllTaskCheckboxes(checkAll) {
            const checkboxes = document.querySelectorAll('#update-tasks-checklist-container .task-item-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkAll;
                const changeEvent = new Event('change', {
                    bubbles: true
                });
                cb.dispatchEvent(changeEvent);
            });
        }

        function updateChecklistCounter() {
            const checkboxes = document.querySelectorAll('#update-tasks-checklist-container .task-item-checkbox');
            const total = checkboxes.length;
            let completed = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) completed++;
            });

            const pending = total - completed;
            const percent = total > 0 ? Math.round((completed / total) * 100) : 0;

            const counter = document.getElementById('update-checklist-counter');
            const pendingCounter = document.getElementById('update-checklist-pending-counter');
            const bar = document.getElementById('update-checklist-progress-bar');

            if (counter) counter.innerText = `${completed} dari ${total} task selesai (${percent}%)`;
            if (pendingCounter) pendingCounter.innerText = `${pending} pending`;
            if (bar) bar.style.width = `${percent}%`;
        }
    </script>
@endsection
