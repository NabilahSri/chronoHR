@extends('layouts.app')

@section('title', 'Laporan Slip Gaji & Pencairan Bank - ChronoHR')
@section('page_title', 'Laporan Rekapitulasi Slip Gaji & Pencairan Bank')
@section('page_subtitle', 'Rekapitulasi keuangan untuk pencairan transfer bank kolektif (Payroll Disbursement), arsip nomor dokumen slip gaji resmi, dan ekspor Excel.')

@section('content')
    @php
        $paydayDate = $payrollSettings['payday_date'] ?? \App\Models\Setting::getValue('payday_date', 25);
        $wfaKartapPct = $payrollSettings['wfa_kartap_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_kartap_pct', 10);
        $wfaPkwtPct = $payrollSettings['wfa_pkwt_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_pkwt_pct', 30);
        $selectedMonthName = \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y');
    @endphp

    <div class="w-full space-y-6">

        <!-- 5 Kartu Ringkasan KPI Pencairan & Pengeluaran Keuangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 print:grid-cols-5">
            <!-- Total Pencairan Gaji Bersih -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Total Pencairan Gaji Bersih</span>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-700 font-mono mt-2">
                    Rp{{ number_format($stats['total_net'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-600 mt-1">Total dana transfer ke karyawan</span>
            </div>

            <!-- Total Pembayaran Lembur -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Total Pembayaran Lembur</span>
                <div class="text-xl sm:text-2xl font-extrabold text-amber-800 font-mono mt-2">
                    +Rp{{ number_format($stats['total_overtime_pay'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-600 mt-1">Akumulasi lembur @ Rp21.000/jam</span>
            </div>

            <!-- Total Insentif Proyek Bulanan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700">Total Insentif Bulanan</span>
                <div class="text-xl sm:text-2xl font-extrabold text-sky-700 font-mono mt-2">
                    +Rp{{ number_format($stats['total_incentives'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-600 mt-1">Honorarium magang & bonus proyek</span>
            </div>

            <!-- Total Potongan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Total Pengurangan Gaji</span>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-700 font-mono mt-2">
                    -Rp{{ number_format($stats['total_wfa_deduction'] + ($stats['total_leave_deduction'] ?? 0) + ($stats['total_sanction_deduction'] ?? 0), 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-600 mt-1">
                    WFA, Izin/Sakit & Denda Sanksi
                </span>
            </div>

            <!-- Kesiapan Rekening Transfer Bank -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Kesiapan Rekening Bank</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    {{ $stats['bank_ready_count'] ?? 0 }}/{{ $stats['total_count'] }} <span class="text-xs font-normal text-slate-600">Rekening</span>
                </div>
                <span class="text-xs text-slate-600 mt-1">
                    {{ $stats['published_count'] }} Slip Terbit · {{ $stats['bank_missing_count'] ?? 0 }} Belum Ada Rek.
                </span>
            </div>
        </div>

        <!-- Kartu Filter Laporan Pencairan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs w-full print:hidden">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Parameter Filter Rekapitulasi Slip Gaji & Pencairan</h3>
                        <p class="text-xs text-slate-600">Pilih bulan, tahun, personil, atau ikatan kerja untuk menyaring daftar transfer bank dan ekspor Excel.</p>
                    </div>
                </div>
                @if (request()->hasAny(['month', 'year', 'user_id', 'employment_type']))
                    <a href="{{ route('reports.payrolls') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.payrolls') }}" class="w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="report_month" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bulan Penggajian
                        </label>
                        <select id="report_month" name="month"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="report_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tahun
                        </label>
                        <select id="report_year" name="year"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="report_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="report_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Personil</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="report_employment_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ikatan Kerja
                        </label>
                        <select id="report_employment_type" name="employment_type"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold">
                            <option value="">Semua Ikatan Kerja</option>
                            <option value="KARTAP" {{ $employmentType === 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap (KARTAP)</option>
                            <option value="PKWT" {{ $employmentType === 'PKWT' ? 'selected' : '' }}>Kontrak Kerja (PKWT)</option>
                            <option value="INTERN" {{ $employmentType === 'INTERN' ? 'selected' : '' }}>Magang / PKL</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full h-[42px] px-5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Fokus Pencairan Transfer Bank & Arsip Slip Gaji -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden w-full">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Slip Penggajian Karyawan & Daftar Transfer Bank</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Periode: <strong class="text-slate-800">{{ $selectedMonthName }}</strong>
                        · Jadwal Pencairan: <strong class="text-slate-900">Tanggal {{ $paydayDate }} {{ $selectedMonthName }}</strong>
                        · Fokus halaman ini adalah rekapitulasi transfer rekening bank dan unduh dokumen resmi.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('reports.payrolls.export', request()->query()) }}"
                        class="h-[38px] inline-flex items-center gap-2 px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-2xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Ekspor ke Excel</span>
                    </a>
                    <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                        {{ $payrolls->total() }} Slip
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">No. Dokumen & Penerima</th>
                            <th class="px-4 py-3.5">Rekening Tujuan Transfer Bank</th>
                            <th class="px-4 py-3.5 text-right">Total Penerimaan Kotor</th>
                            <th class="px-4 py-3.5 text-right">Total Pengurangan</th>
                            <th class="px-4 py-3.5 text-right">Nominal Transfer Bersih (THP)</th>
                            <th class="px-4 py-3.5 text-center">Status Slip</th>
                            <th class="px-5 py-3.5 text-center">Dokumen Slip PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payrolls as $p)
                            @php
                                $docNo = 'CHR-PAY/' . $p->year . sprintf('%02d', $p->month) . '/' . strtoupper(substr($p->id, 0, 8));
                                $totalDeductRow = $p->wfa_deduction_amount + ($p->leave_deduction_amount ?? 0) + ($p->sanction_deduction_amount ?? 0);
                                $slipModalPayload = [
                                    'id' => $p->id,
                                    'docNumber' => $docNo,
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
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- No. Dokumen & Penerima -->
                                <td class="px-5 py-4">
                                    <div class="text-[11px] font-mono font-bold text-slate-500">{{ $docNo }}</div>
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm mt-0.5">{{ $p->user->name ?? 'Karyawan Terhapus' }}</div>
                                    <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span>{{ $p->user->email ?? '-' }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ ($p->user->employment_type ?? '') === 'INTERN' ? 'MAGANG' : ($p->user->employment_type ?? 'KARTAP') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Rekening Tujuan Transfer Bank -->
                                <td class="px-4 py-4 text-xs">
                                    @if (!empty($p->user->bank_account_number))
                                        <div class="font-bold text-slate-900">{{ $p->user->bank_name ?: 'Bank Transfer' }}</div>
                                        <div class="font-mono font-bold text-slate-700 mt-0.5">{{ $p->user->bank_account_number }}</div>
                                        <div class="text-[11px] text-slate-500">a.n. {{ $p->user->bank_account_holder ?: ($p->user->name ?? '-') }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Tunai / Belum Ada Rekening
                                        </span>
                                    @endif
                                </td>

                                <!-- Total Penerimaan Kotor -->
                                <td class="px-4 py-4 text-right font-mono text-xs text-slate-800">
                                    <div class="font-bold text-slate-900">Rp{{ number_format($p->gross_salary, 0, ',', '.') }}</div>
                                    @if ($p->overtime_pay > 0)
                                        <div class="text-[10px] text-emerald-700 font-sans mt-0.5">
                                            Termasuk +Rp{{ number_format($p->overtime_pay, 0, ',', '.') }} ({{ ($p->user->employment_type ?? '') === 'INTERN' ? 'Insentif' : 'Lembur' }})
                                        </div>
                                    @endif
                                </td>

                                <!-- Total Pengurangan -->
                                <td class="px-4 py-4 text-right font-mono text-xs">
                                    @if ($totalDeductRow > 0)
                                        <div class="text-rose-700 font-bold">
                                            -Rp{{ number_format($totalDeductRow, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-sans mt-0.5">
                                            {{ $p->total_wfa_days }} WFA · {{ ($p->total_sick_days ?? 0) + ($p->total_permit_days ?? 0) }} Izin/Skt
                                        </div>
                                    @else
                                        <span class="text-slate-500">Rp0</span>
                                    @endif
                                </td>

                                <!-- Nominal Transfer Bersih (THP) -->
                                <td class="px-4 py-4 text-right font-mono text-xs">
                                    <div class="font-extrabold text-emerald-800 text-sm">
                                        Rp{{ number_format($p->net_salary, 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Status Slip -->
                                <td class="px-4 py-4 text-center">
                                    @if ($p->status === 'PUBLISHED')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Terbit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                            Konsep
                                        </span>
                                    @endif
                                </td>

                                <!-- Dokumen Slip PDF -->
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            onclick='openSlipModal(@json($slipModalPayload), "detail")'
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-300 transition cursor-pointer"
                                            title="Lihat Rincian Slip Lengkap">
                                            <span>Rincian</span>
                                        </button>

                                        <button type="button"
                                            onclick='openSlipModal(@json($slipModalPayload), "preview")'
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-800 hover:bg-slate-100 border border-slate-300 transition cursor-pointer"
                                            title="Pratinjau Dokumen PDF Slip Gaji">
                                            <span>Pratinjau</span>
                                        </button>

                                        <a href="{{ route('reports.payrolls.pdf', $p->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition shadow-2xs"
                                            title="Unduh Berkas PDF">
                                            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-600 text-xs">
                                    Tidak ada data slip gaji pada periode yang dipilih. Silakan kalkulasi penggajian di menu Keuangan atau ubah filter periode.
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
