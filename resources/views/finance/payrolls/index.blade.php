@extends('layouts.app')

@section('title', 'Kalkulasi & Audit Penggajian - ChronoHR')
@section('page_title', 'Meja Kerja Kalkulasi & Audit Penggajian')
@section('page_subtitle', 'Tentukan parameter hari kerja efektif, jalankan kalkulasi otomatis, audit rincian komponen harian (WFA, izin, sanksi, lembur), dan terbitkan slip gaji.')

@section('content')
    @php
        $paydayDate = $payrollSettings['payday_date'] ?? \App\Models\Setting::getValue('payday_date', 25);
        $wfaKartapPct = $payrollSettings['wfa_kartap_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_kartap_pct', 10);
        $wfaPkwtPct = $payrollSettings['wfa_pkwt_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_pkwt_pct', 30);
        $selectedMonthName = \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y');
    @endphp

    <div class="space-y-6">

        <!-- Panel Operasional: Mesin Kalkulasi & Penerbitan Slip Gaji -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-5">
                <!-- Info Status Audit Periode -->
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                            Periode Kalkulasi: {{ $selectedMonthName }}
                        </span>
                        @if ($payrolls->isEmpty() && !request()->hasAny(['user_id', 'employment_type', 'status']))
                            <span class="px-3 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                BELUM DIKALKULASI
                            </span>
                        @elseif($isPublished)
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-md text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                TELAH DITERBITKAN ({{ $stats['published_count'] }} SLIP)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                TAHAP AUDIT KONSEP ({{ $stats['draft_count'] }} KONSEP BELUM RILIS)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600">
                        Parameter Perhitungan: Pembagi <strong>{{ $currentWorkingDays ?? 22 }} Hari Kerja Efektif</strong> · Potongan WFA: <strong>KARTAP {{ $wfaKartapPct }}%</strong> / <strong>PKWT {{ $wfaPkwtPct }}%</strong> · Tarif Lembur: <strong>Rp 21.000/jam</strong>
                    </p>
                </div>

                <!-- Tombol Aksi Kalkulasi, Terbitkan & Pintasan ke Rekap Pencairan Bank -->
                <div class="flex flex-wrap items-center gap-3 pt-4 xl:pt-0 border-t xl:border-t-0 border-slate-100">
                    <!-- Form Kalkulasi Ulang -->
                    <form action="{{ route('finance.payroll.generate') }}" method="POST"
                        class="flex flex-wrap items-center gap-2.5">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">

                        <div class="h-[42px] inline-flex items-center bg-slate-50 border border-slate-300 rounded-xl px-3"
                            title="Jumlah Hari Kerja Efektif Bulanan sebagai pembagi Tarif Harian.">
                            <label for="total_working_days" class="text-xs font-bold text-slate-700 mr-2 whitespace-nowrap">
                                Hari Kerja Efektif:
                            </label>
                            <input type="number" id="total_working_days" name="total_working_days" value="{{ $currentWorkingDays ?? 22 }}" min="1" max="31"
                                class="w-14 px-2 py-1 bg-white border border-slate-300 rounded-lg text-slate-900 text-xs font-bold font-mono text-center focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <span class="text-xs font-semibold text-slate-600 ml-1.5">hr</span>
                        </div>

                        <button type="submit"
                            class="h-[42px] inline-flex items-center gap-2 px-4 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-sky-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>Kalkulasi Penggajian</span>
                        </button>
                    </form>

                    <!-- Form Terbitkan -->
                    @if ($payrolls->isNotEmpty() && !$isPublished)
                        <form action="{{ route('finance.payroll.publish') }}" method="POST"
                            onsubmit="return confirm('Setelah diterbitkan, slip gaji periode {{ $selectedMonthName }} akan rilis ke aplikasi mobile karyawan dan masuk ke Laporan Pencairan Bank. Lanjutkan?')">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit"
                                class="h-[42px] inline-flex items-center gap-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Terbitkan Slip ({{ $stats['draft_count'] }} Konsep)</span>
                            </button>
                        </form>
                    @endif

                    <!-- Tautan ke Laporan Pencairan Transfer Bank -->
                    <a href="{{ route('reports.payrolls', ['month' => $month, 'year' => $year]) }}"
                        class="h-[42px] inline-flex items-center gap-1.5 px-4 bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 rounded-xl text-xs font-bold transition focus-visible:outline-2 focus-visible:outline-sky-600">
                        <span>Rekap Transfer Bank &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Kartu Indikator Audit Perhitungan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Progres Audit Slip</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    {{ $stats['draft_count'] }} <span class="text-xs font-normal text-amber-800">Konsep</span>
                    <span class="text-slate-300 mx-1">/</span>
                    {{ $stats['published_count'] }} <span class="text-xs font-normal text-emerald-700">Terbit</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">Total {{ $stats['total_count'] }} personil terhitung</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Audit Potongan Kehadiran</span>
                <div class="text-xl font-extrabold text-rose-700 font-mono mt-2">
                    -Rp {{ number_format($stats['total_wfa_deduction'] + $stats['total_leave_deduction'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">
                    WFA: Rp{{ number_format($stats['total_wfa_deduction'], 0, ',', '.') }} · Izin/Skt: Rp{{ number_format($stats['total_leave_deduction'], 0, ',', '.') }}
                </span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-800">Audit Denda Sanksi Disiplin</span>
                <div class="text-xl font-extrabold text-rose-800 font-mono mt-2">
                    -Rp {{ number_format($stats['total_sanction_deduction'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Terhubung otomatis dari modul Sanksi</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Hasil Kalkulasi Bersih</span>
                <div class="text-xl font-extrabold text-emerald-700 font-mono mt-2">
                    Rp {{ number_format($stats['total_net'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Termasuk lembur & insentif +Rp{{ number_format($stats['total_overtime_pay'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kartu Filter Audit Kalkulasi -->
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
                        <h3 class="text-sm font-bold text-slate-900">Filter Periode & Personil Slip Gaji</h3>
                        <p class="text-xs text-slate-600">Saring daftar verifikasi berdasarkan periode bulan, tahun, personil, ikatan kerja, atau status konsep/terbit.</p>
                    </div>
                </div>
                @if (request()->hasAny(['month', 'year', 'user_id', 'employment_type', 'status']))
                    <a href="{{ route('finance.payroll.index') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('finance.payroll.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
                    <div>
                        <label for="filter_month" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bulan Penggajian
                        </label>
                        <select id="filter_month" name="month"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="filter_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tahun
                        </label>
                        <select id="filter_year" name="year"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="filter_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="filter_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Personil</option>
                            @foreach ($users ?? [] as $u)
                                <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter_employment_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ikatan Kerja
                        </label>
                        <select id="filter_employment_type" name="employment_type"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Ikatan Kerja</option>
                            <option value="KARTAP" {{ ($employmentType ?? '') === 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap (KARTAP)</option>
                            <option value="PKWT" {{ ($employmentType ?? '') === 'PKWT' ? 'selected' : '' }}>Kontrak Kerja (PKWT)</option>
                            <option value="INTERN" {{ ($employmentType ?? '') === 'INTERN' ? 'selected' : '' }}>Magang / PKL</option>
                        </select>
                    </div>

                    <div>
                        <label for="filter_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Audit Slip
                        </label>
                        <select id="filter_status" name="status"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Status</option>
                            <option value="DRAFT" {{ ($status ?? '') === 'DRAFT' ? 'selected' : '' }}>Konsep (Perlu Audit)</option>
                            <option value="PUBLISHED" {{ ($status ?? '') === 'PUBLISHED' ? 'selected' : '' }}>Diterbitkan</option>
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

        <!-- Tabel Audit Komponen Kalkulasi Harian -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Tabel Audit Komponen Perhitungan Gaji Harian</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Fokus halaman ini adalah memeriksa rincian pembagi hari kerja, potongan WFA ({{ $wfaKartapPct }}% / {{ $wfaPkwtPct }}%), potongan izin/sakit, denda sanksi, dan jam lembur sebelum diterbitkan.
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto shrink-0">
                    {{ $payrolls->total() }} Data Kalkulasi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Pegawai & Ikatan Kerja</th>
                            <th class="px-4 py-3.5 text-right">Gaji Pokok & Tarif Harian</th>
                            <th class="px-4 py-3.5">Audit Rekap Kehadiran</th>
                            <th class="px-4 py-3.5 text-right">Rincian Potongan (WFA / Izin / Sanksi)</th>
                            <th class="px-4 py-3.5 text-right">Tambahan (Lembur / Proyek)</th>
                            <th class="px-4 py-3.5 text-right">Hasil Gaji Bersih</th>
                            <th class="px-4 py-3.5 text-center">Status Audit</th>
                            <th class="px-5 py-3.5 text-center">Tinjau Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($payrolls as $p)
                            @php
                                $totalDeductRow = $p->wfa_deduction_amount + ($p->leave_deduction_amount ?? 0) + ($p->sanction_deduction_amount ?? 0);
                                $slipModalPayload = [
                                    'id' => $p->id,
                                    'docNumber' => 'CHR-PAY/' . $p->year . sprintf('%02d', $p->month) . '/' . strtoupper(substr($p->id, 0, 8)),
                                    'userName' => $p->user->name ?? 'Karyawan',
                                    'userEmail' => $p->user->email ?? '-',
                                    'employmentType' => $p->user->employment_type ?? 'KARTAP',
                                    'bankAccount' => $p->user->bank_name ? ($p->user->bank_name . ' - ' . $p->user->bank_account_number) : 'Tunai / Belum Terdaftar',
                                    'period' => \Carbon\Carbon::create($p->year, $p->month, 1)->translatedFormat('F Y'),
                                    'paydayDate' => (string) $paydayDate,
                                    'wfaKartapPct' => (string) $wfaKartapPct,
                                    'wfaPkwtPct' => (string) $wfaPkwtPct,
                                    'dailyRate' => 'Rp ' . number_format($p->daily_salary_rate, 0, ',', '.') . '/hr',
                                    'workingDays' => (string) $p->total_working_days,
                                    'onsiteDays' => (string) $p->total_present_onsite,
                                    'wfaDays' => (string) $p->total_wfa_days,
                                    'sickDays' => (string) ($p->total_sick_days ?? 0),
                                    'permitDays' => (string) ($p->total_permit_days ?? 0),
                                    'overtimeHours' => (string) $p->total_overtime_hours,
                                    'basicSalary' => 'Rp ' . number_format(($p->user->employment_type ?? '') === 'INTERN' ? ($p->user->basic_salary ?? 0) : ($p->gross_salary - $p->overtime_pay), 0, ',', '.'),
                                    'overtimePay' => 'Rp ' . number_format($p->overtime_pay, 0, ',', '.'),
                                    'grossSalary' => 'Rp ' . number_format($p->gross_salary, 0, ',', '.'),
                                    'wfaDeduction' => 'Rp ' . number_format($p->wfa_deduction_amount, 0, ',', '.'),
                                    'sickDeduction' => 'Rp ' . number_format((($p->total_sick_days ?? 0) * $p->daily_salary_rate) + ($p->sanction_deduction_amount ?? 0), 0, ',', '.'),
                                    'permitDeduction' => 'Rp ' . number_format(($p->total_permit_days ?? 0) * $p->daily_salary_rate, 0, ',', '.'),
                                    'leaveDeduction' => 'Rp ' . number_format($p->leave_deduction_amount ?? 0, 0, ',', '.'),
                                    'totalDeduction' => 'Rp ' . number_format($totalDeductRow, 0, ',', '.'),
                                    'netSalary' => 'Rp ' . number_format($p->net_salary, 0, ',', '.'),
                                    'terbilang' => \App\Helpers\NumberHelper::terbilang($p->net_salary),
                                    'status' => $p->status,
                                    'pdfUrl' => route('reports.payrolls.pdf', $p->id),
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $p->user->name ?? '-' }}</div>
                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                        @if (($p->user->employment_type ?? '') === 'INTERN')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                                MAGANG
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $p->user->employment_type ?? '-' }}
                                            </span>
                                        @endif
                                        <span class="text-[11px] text-slate-500 font-mono">Pembagi: {{ $p->total_working_days }} hr</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <div class="font-bold text-slate-900">Rp {{ number_format($p->user->basic_salary ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-sky-700 text-[11px] font-sans">Uang Saku Magang</div>
                                    @else
                                        <div class="font-bold text-slate-900">Rp {{ number_format($p->user->basic_salary ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-slate-500 text-[11px]">Tarif: Rp {{ number_format($p->daily_salary_rate, 0, ',', '.') }}/hr</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs font-mono space-y-0.5">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <div class="text-xs text-slate-500 font-sans italic">Non-Harian (Berbasis Proyek)</div>
                                    @else
                                        <div>
                                            Kantor: <strong class="text-emerald-800">{{ $p->total_present_onsite }}</strong> hr
                                            · WFA: <strong class="text-sky-800">{{ $p->total_wfa_days }}</strong> hr
                                        </div>
                                        <div class="text-[11px] text-slate-600">
                                            Skt/Izin: <strong class="text-rose-700">{{ ($p->total_sick_days ?? 0) + ($p->total_permit_days ?? 0) }}</strong> hr
                                            · Lembur: <strong class="text-amber-800">{{ $p->total_overtime_hours }}</strong> jam
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN' && $totalDeductRow == 0)
                                        <span class="text-slate-500 font-sans text-xs">Bebas Potongan</span>
                                    @elseif ($totalDeductRow > 0)
                                        <div class="text-rose-700 font-bold">
                                            -Rp {{ number_format($totalDeductRow, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-sans mt-0.5 space-x-1">
                                            @if ($p->wfa_deduction_amount > 0)
                                                <span>WFA: -Rp{{ number_format($p->wfa_deduction_amount, 0, ',', '.') }}</span>
                                            @endif
                                            @if (($p->leave_deduction_amount ?? 0) > 0)
                                                <span>· Izin: -Rp{{ number_format($p->leave_deduction_amount, 0, ',', '.') }}</span>
                                            @endif
                                            @if (($p->sanction_deduction_amount ?? 0) > 0)
                                                <span class="text-rose-700 font-bold">· Sanksi: -Rp{{ number_format($p->sanction_deduction_amount, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-500">Rp 0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-xs">
                                    @if ($p->overtime_pay > 0)
                                        <div class="text-emerald-700 font-bold">+Rp {{ number_format($p->overtime_pay, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-slate-500 font-sans">
                                            {{ ($p->user->employment_type ?? '') === 'INTERN' ? 'Insentif Proyek' : $p->total_overtime_hours . ' jam lembur / bonus' }}
                                        </div>
                                    @else
                                        <span class="text-slate-500">Rp 0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-sm font-extrabold text-slate-900">
                                    Rp {{ number_format($p->net_salary, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold {{ $p->status === 'PUBLISHED' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-900 border border-amber-200' }}">
                                        {{ $p->status === 'PUBLISHED' ? 'TERBIT' : 'KONSEP' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            onclick='openSlipModal(@json($slipModalPayload), "detail")'
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-300 transition cursor-pointer"
                                            title="Audit Rincian Komponen">
                                            <span>Rincian</span>
                                        </button>

                                        <button type="button"
                                            onclick='openSlipModal(@json($slipModalPayload), "preview")'
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-800 hover:bg-slate-100 border border-slate-300 transition cursor-pointer"
                                            title="Pratinjau Slip PDF">
                                            <span>Pratinjau</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <p class="font-bold text-slate-700 text-sm">
                                        Belum ada data kalkulasi gaji untuk periode {{ $selectedMonthName }}
                                    </p>
                                    <p class="text-xs text-slate-600 mt-1">
                                        Gunakan tombol "Kalkulasi Penggajian" di bagian atas untuk menghitung konsep slip gaji otomatis dari data presensi.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $payrolls])
        </div>

        @include('partials.slip-modal')
    </div>
@endsection
