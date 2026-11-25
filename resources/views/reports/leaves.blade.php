@extends('layouts.app')

@section('title', 'Laporan Pengajuan - ChronoHR')
@section('page_title', 'Laporan Rekapitulasi Pengajuan')
@section('page_subtitle', 'Pemantauan permohonan cuti tahunan, sakit, izin kerja, dan permohonan kerja jarak jauh (WFA) personil.')

@section('content')
    <div class="w-full space-y-6">

        <!-- 5 KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 print:grid-cols-5">
            <!-- Total Pengajuan -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pengajuan</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">{{ $stats['total'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Cuti, Izin, Sakit & WFA</span>
            </div>

            <!-- Disetujui -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Disetujui</span>
                <div class="text-2xl font-extrabold text-emerald-600 font-mono mt-2">{{ $stats['approved'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Presensi sinkron otomatis</span>
            </div>

            <!-- Menunggu Tinjauan -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Menunggu</span>
                <div class="text-2xl font-extrabold text-amber-600 font-mono mt-2">{{ $stats['pending'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Perlu keputusan atasan</span>
            </div>

            <!-- Ditolak -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Ditolak</span>
                <div class="text-2xl font-extrabold text-rose-600 font-mono mt-2">{{ $stats['rejected'] }}</div>
                <span class="text-[10px] text-slate-500 mt-1">Permohonan tidak disetujui</span>
            </div>

            <!-- Breakdown Tipe -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Komposisi Pengajuan</span>
                <div class="text-xs font-mono font-bold text-slate-700 mt-2 space-y-1">
                    <div>WFA: <span class="text-sky-600 font-extrabold">{{ $stats['wfa_count'] }}</span></div>
                    <div>Sakit / Izin: <span
                            class="text-indigo-600 font-extrabold">{{ $stats['sakit_count'] + $stats['izin_count'] }}</span>
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1">Distribusi jenis permohonan</span>
            </div>
        </div>

        <!-- Kartu Filter Rekapitulasi Pengajuan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs w-full print:hidden">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Parameter Filter Rekapitulasi Pengajuan</h3>
                        <p class="text-xs text-slate-600">Pilih rentang tanggal, personil, jenis pengajuan, atau status keputusan untuk menyaring laporan.</p>
                    </div>
                </div>
                @if (request()->hasAny(['start_date', 'end_date', 'user_id', 'type', 'status']))
                    <a href="{{ route('reports.leaves') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.leaves') }}" class="w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
                    <!-- Dari Tanggal -->
                    <div>
                        <label for="rep_leave_start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Dari Tanggal
                        </label>
                        <input type="date" id="rep_leave_start_date" name="start_date" value="{{ $startDate }}"
                            class="w-full h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label for="rep_leave_end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Sampai Tanggal
                        </label>
                        <input type="date" id="rep_leave_end_date" name="end_date" value="{{ $endDate }}"
                            class="w-full h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                    </div>

                    <!-- Personil -->
                    <div>
                        <label for="rep_leave_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="rep_leave_user_id" name="user_id"
                            class="w-full h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Personil</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipe Pengajuan -->
                    <div>
                        <label for="rep_leave_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jenis Pengajuan
                        </label>
                        <select id="rep_leave_type" name="type"
                            class="w-full h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Jenis</option>
                            <option value="WFA" {{ $type === 'WFA' ? 'selected' : '' }}>WFA (Kerja Jarak Jauh)</option>
                            <option value="SAKIT" {{ $type === 'SAKIT' ? 'selected' : '' }}>Sakit</option>
                            <option value="IZIN" {{ $type === 'IZIN' ? 'selected' : '' }}>Izin Keperluan Pribadi</option>
                        </select>
                    </div>

                    <!-- Status Pengajuan -->
                    <div>
                        <label for="rep_leave_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Pengajuan
                        </label>
                        <select id="rep_leave_status" name="status"
                            class="w-full h-[42px] px-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Status</option>
                            <option value="APPROVED" {{ $status === 'APPROVED' ? 'selected' : '' }}>Disetujui</option>
                            <option value="PENDING" {{ $status === 'PENDING' ? 'selected' : '' }}>Menunggu</option>
                            <option value="REJECTED" {{ $status === 'REJECTED' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <!-- Tombol Terapkan -->
                    <div>
                        <button type="submit"
                            class="w-full h-[42px] px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-sky-600">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Leaves Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs w-full">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Riwayat Pengajuan Cuti & Izin</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Periode: <strong
                            class="text-slate-700">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong>
                        s/d <strong
                            class="text-slate-700">{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('reports.leaves.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-xs font-semibold shadow-2xs hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Ekspor ke Excel</span>
                    </a>
                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ $leaves->total() }} Permohonan
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Karyawan Pemohon</th>
                            <th class="px-6 py-4">Jenis & Durasi</th>
                            <th class="px-6 py-4">Rentang Tanggal</th>
                            <th class="px-6 py-4">Alasan / Keperluan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4">Penyetuju</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($leaves as $lv)
                            @php
                                $start = \Carbon\Carbon::parse($lv->start_date);
                                $end = \Carbon\Carbon::parse($lv->end_date);
                                $days = $start->diffInDays($end) + 1;
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Karyawan -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $lv->user->name ?? 'Karyawan Terhapus' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        {{ $lv->user->email ?? '-' }}
                                    </div>
                                </td>

                                <!-- Jenis & Durasi -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if ($lv->type === 'WFA')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                WFA (Jarak Jauh)
                                            </span>
                                        @elseif($lv->type === 'SAKIT')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sakit
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Izin
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-mono font-bold text-slate-500 mt-1">
                                        Durasi: {{ $days }} Hari
                                    </div>
                                </td>

                                <!-- Rentang Tanggal -->
                                <td class="px-6 py-4 font-mono text-xs text-slate-700">
                                    <div>{{ $start->translatedFormat('d M Y') }}</div>
                                    @if ($days > 1)
                                        <div class="text-[11px] text-slate-400">s/d {{ $end->translatedFormat('d M Y') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Alasan -->
                                <td class="px-6 py-4 text-xs">
                                    <p class="text-slate-800 leading-relaxed max-w-xs">{{ $lv->reason }}</p>
                                    @if ($lv->rejection_reason)
                                        <div
                                            class="mt-1 text-[11px] text-rose-600 bg-rose-50 p-2 rounded-lg border border-rose-200 leading-normal">
                                            <strong>Catatan Penolakan:</strong> {{ $lv->rejection_reason }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center">
                                    @if ($lv->status === 'APPROVED')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Disetujui
                                        </span>
                                    @elseif($lv->status === 'PENDING')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                <!-- Penyetuju -->
                                <td class="px-6 py-4 text-xs">
                                    @if ($lv->approver)
                                        <div class="font-bold text-slate-900">{{ $lv->approver->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $lv->updated_at->translatedFormat('d M Y H:i') }}</div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data riwayat permohonan yang sesuai kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $leaves])
        </div>

    </div>
@endsection
