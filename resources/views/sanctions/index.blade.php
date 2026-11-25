@extends('layouts.app')

@section('title', 'Manajemen Sanksi & Kedisiplinan - ChronoHR')
@section('page_title', 'Manajemen Sanksi & Aturan Kedisiplinan')
@section('page_subtitle', 'Kelola master jenis pelanggaran secara dinamis, deteksi otomatis keterlambatan berulang, serta penerbitan Surat Peringatan (SP) dan denda slip gaji.')

@section('content')
    @php
        $canManageSanctions = auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('finance');
        $selectedPeriodLabel = \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y');
    @endphp

    <div class="space-y-6">

        <!-- 4 Kartu Ringkasan KPI Sanksi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sanksi Aktif ({{ $selectedPeriodLabel }})</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    {{ $stats['total_active'] }} <span class="text-xs font-normal text-slate-500">Catatan</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">
                    {{ $stats['teguran_count'] }} Teguran Lisan · {{ $stats['sp_count'] }} Surat Peringatan
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Surat Peringatan (SP1 - SP3)</span>
                <div class="text-2xl font-extrabold text-rose-700 font-mono mt-2">
                    {{ $stats['sp_count'] }} <span class="text-xs font-normal text-slate-500">Pegawai</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">Tercatat pada evaluasi disiplin SDM</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Deteksi Otomatis Presensi</span>
                <div class="text-2xl font-extrabold text-amber-800 font-mono mt-2">
                    {{ $stats['auto_detected_count'] }} <span class="text-xs font-normal text-slate-500">Pelanggaran</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">Keterlambatan / alpa melebihi batas</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Potongan Denda Slip Gaji</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    Rp {{ number_format($stats['total_deduction'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Otomatis memotong slip gaji periode ini</span>
            </div>
        </div>

        @if ($canManageSanctions)
            <!-- Panel Aksi Operasional: Deteksi Otomatis & Input Sanksi Manual -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Pusat Kendali Penindakan & Deteksi Pelanggaran</h2>
                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                Periode: {{ $selectedPeriodLabel }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Jalankan deteksi otomatis untuk memeriksa karyawan yang terlambat/alpa melebihi batas aturan, atau catat sanksi pelanggaran secara manual.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Form Deteksi Otomatis -->
                        <form action="{{ route('sanctions.detect') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit"
                                class="h-[42px] inline-flex items-center gap-2 px-4 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Deteksi Otomatis dari Presensi</span>
                            </button>
                        </form>

                        <!-- Tombol Catat Sanksi Manual -->
                        <button type="button" onclick="openIssueSanctionModal()"
                            class="h-[42px] inline-flex items-center gap-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Catat Sanksi Karyawan</span>
                        </button>

                        <!-- Tombol Tambah Jenis Pelanggaran -->
                        <button type="button" onclick="openCreateRuleModal()"
                            class="h-[42px] inline-flex items-center gap-2 px-4 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 rounded-xl text-xs font-bold transition cursor-pointer">
                            <span>+ Tambah Jenis Pelanggaran</span>
                        </button>
                    </div>
                </div>

                @if (!empty($autoCandidates))
                    <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 space-y-2">
                        <div class="text-xs font-bold text-amber-900 uppercase tracking-wider">
                            Peringatan Sistem: Ditemukan {{ count($autoCandidates) }} Pelanggaran Ambang Batas Presensi ({{ $selectedPeriodLabel }})
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            @foreach ($autoCandidates as $cand)
                                <div class="p-3 rounded-lg bg-white border border-amber-200 flex items-center justify-between gap-2 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $cand['user']->name }} ({{ $cand['user']->employment_type }})</div>
                                        <div class="text-slate-600">{{ $cand['metric_label'] }} - Aturan: <strong>{{ $cand['rule']->name }}</strong></div>
                                    </div>
                                    @if ($cand['already_issued'])
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                            SUDAH TERBIT
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-900 border border-amber-300 shrink-0">
                                            BELUM DITERBITKAN
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Kartu Filter Riwayat Sanksi -->
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
                        <h3 class="text-sm font-bold text-slate-900">Filter Riwayat Sanksi & Surat Peringatan</h3>
                        <p class="text-xs text-slate-600">Saring daftar sanksi berdasarkan bulan, tahun, personil karyawan, tingkat sanksi, atau status berlaku.</p>
                    </div>
                </div>
                @if (request()->hasAny(['month', 'year', 'user_id', 'level', 'status']))
                    <a href="{{ route('sanctions.index') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('sanctions.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
                    <div>
                        <label for="sanc_filter_month" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bulan
                        </label>
                        <select id="sanc_filter_month" name="month"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="sanc_filter_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tahun
                        </label>
                        <select id="sanc_filter_year" name="year"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="sanc_filter_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="sanc_filter_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Personil</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sanc_filter_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tingkat Sanksi
                        </label>
                        <select id="sanc_filter_level" name="level"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Tingkat</option>
                            <option value="TEGURAN_LISAN" {{ ($level ?? '') === 'TEGURAN_LISAN' ? 'selected' : '' }}>Teguran Lisan</option>
                            <option value="SP1" {{ ($level ?? '') === 'SP1' ? 'selected' : '' }}>Surat Peringatan 1 (SP1)</option>
                            <option value="SP2" {{ ($level ?? '') === 'SP2' ? 'selected' : '' }}>Surat Peringatan 2 (SP2)</option>
                            <option value="SP3" {{ ($level ?? '') === 'SP3' ? 'selected' : '' }}>Surat Peringatan 3 (SP3)</option>
                        </select>
                    </div>

                    <div>
                        <label for="sanc_filter_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Sanksi
                        </label>
                        <select id="sanc_filter_status" name="status"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Status</option>
                            <option value="ACTIVE" {{ ($status ?? '') === 'ACTIVE' ? 'selected' : '' }}>Aktif Berlaku</option>
                            <option value="RESOLVED" {{ ($status ?? '') === 'RESOLVED' ? 'selected' : '' }}>Selesai Pembinaan</option>
                            <option value="REVOKED" {{ ($status ?? '') === 'REVOKED' ? 'selected' : '' }}>Dibebaskan / Dicabut</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full h-[42px] px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Sanksi Karyawan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Sanksi & Surat Peringatan Karyawan</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Periode: <strong>{{ $selectedPeriodLabel }}</strong> · Sanksi berstatus <strong>Aktif</strong> dengan nominal denda akan otomatis diperhitungkan pada Kalkulasi Penggajian.
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    {{ $sanctions->total() }} Data Sanksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Karyawan</th>
                            <th class="px-4 py-3.5">Tanggal & Sumber</th>
                            <th class="px-4 py-3.5">Jenis Pelanggaran</th>
                            <th class="px-4 py-3.5">Tingkat Sanksi</th>
                            <th class="px-4 py-3.5 text-right">Potongan Gaji</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            @if ($canManageSanctions)
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            @endif
                            <th class="px-5 py-3.5 text-right">Dokumen & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sanctions as $sanc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $sanc->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 font-mono">
                                        {{ $sanc->user->email ?? '' }} · {{ ($sanc->user->employment_type ?? '') === 'INTERN' ? 'MAGANG' : ($sanc->user->employment_type ?? '') }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs">
                                    <div class="font-mono font-bold text-slate-900">{{ \Carbon\Carbon::parse($sanc->incident_date)->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $sanc->source === 'AUTO_DETECTED' ? 'Otomatis Sistem (' . $sanc->occurrence_count . 'x)' : 'Input Manual HRD' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs max-w-xs">
                                    <div class="font-bold text-slate-900">{{ $sanc->violation_name }}</div>
                                    <div class="text-slate-600 mt-0.5 line-clamp-2">{{ $sanc->reason }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($sanc->level === 'TEGURAN_LISAN')
                                        <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                            TEGURAN LISAN
                                        </span>
                                    @elseif ($sanc->level === 'SP1')
                                        <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-orange-50 text-orange-900 border border-orange-300">
                                            SURAT PERINGATAN 1 (SP1)
                                        </span>
                                    @elseif ($sanc->level === 'SP2')
                                        <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                            SURAT PERINGATAN 2 (SP2)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-rose-900 text-white">
                                            SURAT PERINGATAN 3 (SP3)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-xs font-bold whitespace-nowrap">
                                    @if ($sanc->deduction_amount > 0)
                                        <span class="text-rose-700">-Rp {{ number_format($sanc->deduction_amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-500 font-normal">Rp 0 (Administratif)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if ($sanc->status === 'ACTIVE')
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                            AKTIF
                                        </span>
                                    @elseif ($sanc->status === 'RESOLVED')
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            SELESAI
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            DICABUT
                                        </span>
                                    @endif
                                </td>
                                @if ($canManageSanctions)
                                    @php
                                        $sancEditPayload = [
                                            'updateUrl' => route('sanctions.update', $sanc->id),
                                            'userName' => $sanc->user->name ?? '-',
                                            'violationName' => $sanc->violation_name,
                                            'level' => $sanc->level,
                                            'deductionAmount' => (float) $sanc->deduction_amount,
                                            'status' => $sanc->status,
                                            'reason' => $sanc->reason,
                                        ];
                                    @endphp
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <button type="button"
                                            onclick='openEditSanctionModal(@json($sancEditPayload))'
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 transition cursor-pointer">
                                            Ubah / Status
                                        </button>
                                    </td>
                                @endif
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <a href="{{ route('sanctions.pdf', ['employeeSanction' => $sanc->id, 'preview' => 1]) }}"
                                            target="_blank"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 transition">
                                            Pratinjau SP
                                        </a>
                                        <a href="{{ route('sanctions.pdf', $sanc->id) }}"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white transition">
                                            Unduh PDF
                                        </a>
                                        @if ($canManageSanctions)
                                            @php
                                                $sancEditPayload = [
                                                    'updateUrl' => route('sanctions.update', $sanc->id),
                                                    'userName' => $sanc->user->name ?? '-',
                                                    'violationName' => $sanc->violation_name,
                                                    'level' => $sanc->level,
                                                    'deductionAmount' => (float) $sanc->deduction_amount,
                                                    'status' => $sanc->status,
                                                    'reason' => $sanc->reason,
                                                ];
                                            @endphp
                                            <button type="button"
                                                onclick='openEditSanctionModal(@json($sancEditPayload))'
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 transition cursor-pointer">
                                                Ubah / Status
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageSanctions ? 7 : 6 }}" class="px-6 py-12 text-center text-slate-500 text-xs">
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    Belum ada catatan sanksi atau surat peringatan pada periode {{ $selectedPeriodLabel }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $sanctions])
        </div>

        <!-- TABEL MASTER JENIS PELANGGARAN & ATURAN SANKSI (KONFIGURASI MANUAL) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Master Jenis Kesalahan & Aturan Sanksi (Dapat Diatur Manual)</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Atur daftar kesalahan yang mendapat sanksi, apakah dipicu otomatis dari jumlah terlambat/alpa (<code>&gt; N kali</code>) atau diinput manual, beserta tingkat SP dan denda slip gaji.
                    </p>
                </div>
                @if ($canManageSanctions)
                    <button type="button" onclick="openCreateRuleModal()"
                        class="h-[38px] px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition cursor-pointer shrink-0">
                        + Tambah Aturan Baru
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Kode & Nama Pelanggaran</th>
                            <th class="px-4 py-3.5">Kategori</th>
                            <th class="px-4 py-3.5">Pemicu (Trigger)</th>
                            <th class="px-4 py-3.5">Tingkat Sanksi Bawaan</th>
                            <th class="px-4 py-3.5 text-right">Denda Slip Gaji Bawaan</th>
                            <th class="px-4 py-3.5 text-center">Status Aturan</th>
                            @if ($canManageSanctions)
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rules as $rule)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                            {{ $rule->code }}
                                        </span>
                                        <span class="font-bold text-slate-900">{{ $rule->name }}</span>
                                    </div>
                                    @if ($rule->description)
                                        <p class="text-xs text-slate-500 mt-1">{{ $rule->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs font-semibold text-slate-700">
                                    {{ \App\Models\SanctionRule::categoryLabel($rule->category) }}
                                </td>
                                <td class="px-4 py-4 text-xs">
                                    @if ($rule->trigger_type === 'AUTO_LATE')
                                        <span class="px-2.5 py-0.5 rounded-md font-mono font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                            Otomatis: Terlambat &gt; {{ $rule->threshold_count }}x
                                        </span>
                                    @elseif ($rule->trigger_type === 'AUTO_ALPHA')
                                        <span class="px-2.5 py-0.5 rounded-md font-mono font-bold bg-rose-50 text-rose-800 border border-rose-300">
                                            Otomatis: Alpa &gt; {{ $rule->threshold_count }}x
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                            Input Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs font-bold text-slate-900">
                                    {{ \App\Models\SanctionRule::levelLabel($rule->default_level) }}
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-xs font-bold">
                                    @if ($rule->default_deduction_amount > 0)
                                        <span class="text-rose-700">Rp {{ number_format($rule->default_deduction_amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-500 font-normal">Rp 0 (Hanya SP)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($rule->is_active)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">AKTIF</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-500 border border-slate-200">NONAKTIF</span>
                                    @endif
                                </td>
                                @if ($canManageSanctions)
                                    @php
                                        $ruleEditPayload = [
                                            'updateUrl' => route('sanctions.rules.update', $rule->id),
                                            'code' => $rule->code,
                                            'name' => $rule->name,
                                            'category' => $rule->category,
                                            'triggerType' => $rule->trigger_type,
                                            'thresholdCount' => $rule->threshold_count,
                                            'defaultLevel' => $rule->default_level,
                                            'defaultDeduction' => (float) $rule->default_deduction_amount,
                                            'description' => $rule->description,
                                            'isActive' => (bool) $rule->is_active,
                                        ];
                                    @endphp
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button"
                                                onclick='openEditRuleModal(@json($ruleEditPayload))'
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 transition cursor-pointer">
                                                Ubah
                                            </button>
                                            <form action="{{ route('sanctions.rules.destroy', $rule->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus aturan pelanggaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($canManageSanctions)
        <!-- MODAL 1: CATAT SANKSI KARYAWAN BARU -->
        <div id="issueSanctionModal" role="dialog" aria-modal="true"
            onclick="if(event.target === this) document.getElementById('issueSanctionModal').classList.add('hidden')"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 flex items-center justify-center">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="text-base font-bold text-slate-900">Catat Sanksi / Surat Peringatan Karyawan</h3>
                    <button type="button" onclick="document.getElementById('issueSanctionModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <form action="{{ route('sanctions.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="issue_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Karyawan
                        </label>
                        <select id="issue_user_id" name="user_id" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                            <option value="">Pilih Karyawan...</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="issue_rule_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Jenis Pelanggaran (Dari Master Aturan)
                        </label>
                        <select id="issue_rule_id" name="sanction_rule_id" onchange="applyRuleDefaults(this)"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                            <option value="" data-level="TEGURAN_LISAN" data-deduction="0">-- Pilih dari Master Aturan atau Ketik Manual di Bawah --</option>
                            @foreach ($rules->where('is_active', true) as $r)
                                <option value="{{ $r->id }}"
                                    data-name="{{ $r->name }}"
                                    data-level="{{ $r->default_level }}"
                                    data-deduction="{{ (float) $r->default_deduction_amount }}">
                                    [{{ $r->code }}] {{ $r->name }} ({{ \App\Models\SanctionRule::levelLabel($r->default_level) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="issue_custom_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Pelanggaran (Dapat Disesuaikan Manual)
                        </label>
                        <input type="text" id="issue_custom_name" name="custom_violation_name" placeholder="Contoh: Terlambat Masuk > 3 Kali / Pelanggaran SOP..."
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="issue_incident_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Penindakan
                            </label>
                            <input type="date" id="issue_incident_date" name="incident_date" value="{{ now()->toDateString() }}" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                        </div>

                        <div>
                            <label for="issue_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tingkat Sanksi
                            </label>
                            <select id="issue_level" name="level" data-no-search="true" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                                <option value="TEGURAN_LISAN">Teguran Lisan</option>
                                <option value="SP1">Surat Peringatan 1 (SP1)</option>
                                <option value="SP2">Surat Peringatan 2 (SP2)</option>
                                <option value="SP3">Surat Peringatan 3 (SP3)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="issue_deduction" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Potongan / Denda Slip Gaji (Rp)
                        </label>
                        <input type="number" id="issue_deduction" name="deduction_amount" value="0" min="0" step="1000" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 mt-1">Isi 0 jika sanksi hanya berupa Surat Peringatan (SP) tanpa memotong gaji.</p>
                    </div>

                    <div>
                        <label for="issue_reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Kronologi Pelanggaran
                        </label>
                        <textarea id="issue_reason" name="reason" rows="2" required placeholder="Jelaskan alasan pemberian sanksi..."
                            class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('issueSanctionModal').classList.add('hidden')"
                            class="h-[40px] px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="h-[40px] px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 cursor-pointer">
                            Terbitkan Sanksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: UBAH STATUS / RINCIAN SANKSI -->
        <div id="editSanctionModal" role="dialog" aria-modal="true"
            onclick="if(event.target === this) document.getElementById('editSanctionModal').classList.add('hidden')"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 flex items-center justify-center">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Perbarui Status Sanksi</h3>
                        <p class="text-xs text-slate-600" id="edit_sanc_subtitle">-</p>
                    </div>
                    <button type="button" onclick="document.getElementById('editSanctionModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <form id="edit_sanc_form" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="edit_sanc_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tingkat Sanksi
                        </label>
                        <select id="edit_sanc_level" name="level" data-no-search="true" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                            <option value="TEGURAN_LISAN">Teguran Lisan</option>
                            <option value="SP1">Surat Peringatan 1 (SP1)</option>
                            <option value="SP2">Surat Peringatan 2 (SP2)</option>
                            <option value="SP3">Surat Peringatan 3 (SP3)</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_sanc_deduction" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Potongan Gaji (Rp)
                        </label>
                        <input type="number" id="edit_sanc_deduction" name="deduction_amount" min="0" step="1000" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label for="edit_sanc_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Berlaku
                        </label>
                        <select id="edit_sanc_status" name="status" data-no-search="true" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                            <option value="ACTIVE">Aktif Berlaku</option>
                            <option value="RESOLVED">Selesai Pembinaan</option>
                            <option value="REVOKED">Dicabut / Dibebaskan</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_sanc_reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan / Alasan
                        </label>
                        <textarea id="edit_sanc_reason" name="reason" rows="2" required
                            class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('editSanctionModal').classList.add('hidden')"
                            class="h-[40px] px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="h-[40px] px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 3: TAMBAH / UBAH MASTER ATURAN JENIS PELANGGARAN -->
        <div id="ruleModal" role="dialog" aria-modal="true"
            onclick="if(event.target === this) document.getElementById('ruleModal').classList.add('hidden')"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 flex items-center justify-center">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 id="rule_modal_title" class="text-base font-bold text-slate-900">Tambah Aturan Jenis Pelanggaran</h3>
                    <button type="button" onclick="document.getElementById('ruleModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <form id="rule_modal_form" method="POST" action="{{ route('sanctions.rules.store') }}" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="rule_modal_method" name="_method" value="POST">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="rule_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kode Aturan
                            </label>
                            <input type="text" id="rule_code" name="code" placeholder="Misal: SOP-02" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold uppercase">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="rule_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Jenis Pelanggaran / Kesalahan
                            </label>
                            <input type="text" id="rule_name" name="name" placeholder="Contoh: Terlambat Masuk > 3 Kali Sebulan" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rule_category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kategori Pelanggaran
                            </label>
                            <select id="rule_category" name="category" data-no-search="true" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                                <option value="DISIPLIN_WAKTU">Kedisiplinan Jam Kerja</option>
                                <option value="KEHADIRAN">Kehadiran & Presensi</option>
                                <option value="KINERJA_TUGAS">Kinerja & Laporan Tugas</option>
                                <option value="ETIKA_SOP">Etika & Kepatuhan SOP</option>
                            </select>
                        </div>

                        <div>
                            <label for="rule_trigger_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Mekanisme Pemicu (Trigger)
                            </label>
                            <select id="rule_trigger_type" name="trigger_type" data-no-search="true" onchange="toggleRuleThresholdInput()" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                                <option value="MANUAL">Input Manual oleh Admin / HRD</option>
                                <option value="AUTO_LATE">Otomatis: Terlambat Masuk &gt; N Kali</option>
                                <option value="AUTO_ALPHA">Otomatis: Alpa Tanpa Keterangan &gt; N Kali</option>
                            </select>
                        </div>
                    </div>

                    <div id="rule_threshold_wrapper" class="hidden">
                        <label for="rule_threshold_count" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Batas Jumlah Pelanggaran Bulanan (N Kali)
                        </label>
                        <input type="number" id="rule_threshold_count" name="threshold_count" min="1" max="31" value="3"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                        <p class="text-[11px] text-slate-500 mt-1">Misal diisi 3: sanksi otomatis diberikan jika karyawan terlambat lebih dari 3 kali dalam sebulan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rule_default_level" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tingkat Sanksi Bawaan
                            </label>
                            <select id="rule_default_level" name="default_level" data-no-search="true" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                                <option value="TEGURAN_LISAN">Teguran Lisan</option>
                                <option value="SP1">Surat Peringatan 1 (SP1)</option>
                                <option value="SP2">Surat Peringatan 2 (SP2)</option>
                                <option value="SP3">Surat Peringatan 3 (SP3)</option>
                            </select>
                        </div>

                        <div>
                            <label for="rule_default_deduction" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Potongan Denda Bawaan (Rp)
                            </label>
                            <input type="number" id="rule_default_deduction" name="default_deduction_amount" min="0" step="1000" value="0" required
                                class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                        </div>
                    </div>

                    <div>
                        <label for="rule_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Deskripsi Aturan (Opsional)
                        </label>
                        <input type="text" id="rule_description" name="description" placeholder="Penjelasan singkat aturan sanksi..."
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="rule_is_active" name="is_active" value="1" checked
                            class="w-4 h-4 rounded border-slate-300 text-slate-900">
                        <label for="rule_is_active" class="text-xs font-semibold text-slate-700">Aturan pelanggaran ini aktif berlaku</label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('ruleModal').classList.add('hidden')"
                            class="h-[40px] px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="h-[40px] px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 cursor-pointer">
                            Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openIssueSanctionModal() {
                document.getElementById('issueSanctionModal').classList.remove('hidden');
            }

            function applyRuleDefaults(selectEl) {
                const opt = selectEl.options[selectEl.selectedIndex];
                if (!opt) return;
                const name = opt.getAttribute('data-name') || '';
                const level = opt.getAttribute('data-level') || 'TEGURAN_LISAN';
                const deduction = opt.getAttribute('data-deduction') || '0';

                if (name) {
                    document.getElementById('issue_custom_name').value = name;
                }
                document.getElementById('issue_level').value = level;
                document.getElementById('issue_deduction').value = deduction;
            }

            function openEditSanctionModal(data) {
                document.getElementById('edit_sanc_form').action = data.updateUrl;
                document.getElementById('edit_sanc_subtitle').textContent = data.userName + ' - ' + data.violationName;
                document.getElementById('edit_sanc_level').value = data.level;
                document.getElementById('edit_sanc_deduction').value = data.deductionAmount || 0;
                document.getElementById('edit_sanc_status').value = data.status;
                document.getElementById('edit_sanc_reason').value = data.reason || '';
                document.getElementById('editSanctionModal').classList.remove('hidden');
            }

            function toggleRuleThresholdInput() {
                const trigger = document.getElementById('rule_trigger_type').value;
                const wrapper = document.getElementById('rule_threshold_wrapper');
                if (trigger === 'AUTO_LATE' || trigger === 'AUTO_ALPHA') {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                }
            }

            function openCreateRuleModal() {
                document.getElementById('rule_modal_title').textContent = 'Tambah Aturan Jenis Pelanggaran Baru';
                document.getElementById('rule_modal_form').action = '{{ route('sanctions.rules.store') }}';
                document.getElementById('rule_modal_method').value = 'POST';
                document.getElementById('rule_code').disabled = false;
                document.getElementById('rule_code').value = '';
                document.getElementById('rule_name').value = '';
                document.getElementById('rule_category').value = 'DISIPLIN_WAKTU';
                document.getElementById('rule_trigger_type').value = 'MANUAL';
                document.getElementById('rule_threshold_count').value = 3;
                document.getElementById('rule_default_level').value = 'TEGURAN_LISAN';
                document.getElementById('rule_default_deduction').value = 0;
                document.getElementById('rule_description').value = '';
                document.getElementById('rule_is_active').checked = true;
                toggleRuleThresholdInput();
                document.getElementById('ruleModal').classList.remove('hidden');
            }

            function openEditRuleModal(data) {
                document.getElementById('rule_modal_title').textContent = 'Ubah Aturan Jenis Pelanggaran';
                document.getElementById('rule_modal_form').action = data.updateUrl;
                document.getElementById('rule_modal_method').value = 'PUT';
                document.getElementById('rule_code').value = data.code;
                document.getElementById('rule_code').disabled = true;
                document.getElementById('rule_name').value = data.name;
                document.getElementById('rule_category').value = data.category;
                document.getElementById('rule_trigger_type').value = data.triggerType;
                document.getElementById('rule_threshold_count').value = data.thresholdCount || 3;
                document.getElementById('rule_default_level').value = data.defaultLevel;
                document.getElementById('rule_default_deduction').value = data.defaultDeduction || 0;
                document.getElementById('rule_description').value = data.description || '';
                document.getElementById('rule_is_active').checked = !!data.isActive;
                toggleRuleThresholdInput();
                document.getElementById('ruleModal').classList.remove('hidden');
            }
        </script>
    @endif
@endsection
