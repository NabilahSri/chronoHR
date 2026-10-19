@extends('layouts.app')

@section('title', 'Laporan Slip Gaji — ChronoHR')
@section('page_title', 'Laporan Rekapitulasi Slip Gaji')
@section('page_subtitle',
    'Rekapitulasi beban penggajian bulanan, pemotongan WFH berdasar ikatan kerja, dan insentif
    lembur.')

@section('content')
    <div class="w-full space-y-6">

        <!-- 5 KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 print:grid-cols-5">
            <!-- Total Net Expense -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Total Beban Gaji Bersih</span>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 font-mono mt-2">
                    Rp{{ number_format($stats['total_net'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-500 mt-1">Gaji netto yang dibayarkan</span>
            </div>

            <!-- Total Gross -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Gaji Pokok Kotor</span>
                <div class="text-xl sm:text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    Rp{{ number_format($stats['total_gross'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-500 mt-1">Sebelum potongan & tunjangan</span>
            </div>

            <!-- Total Deductions -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Total Potongan (WFH & Sakit/Izin)</span>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-600 font-mono mt-2">
                    -Rp{{ number_format($stats['total_wfh_deduction'] + ($stats['total_leave_deduction'] ?? 0), 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-500 mt-1">WFH: -Rp{{ number_format($stats['total_wfh_deduction'], 0, ',', '.') }} · Sakit/Izin: -Rp{{ number_format($stats['total_leave_deduction'] ?? 0, 0, ',', '.') }}</span>
            </div>

            <!-- Total Overtime Pay -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Total Uang Lembur</span>
                <div class="text-xl sm:text-2xl font-extrabold text-amber-600 font-mono mt-2">
                    +Rp{{ number_format($stats['total_overtime_pay'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-500 mt-1">Tarif resmi Rp21rb/jam</span>
            </div>

            <!-- Karyawan Tergaji -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Karyawan Tergaji</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    {{ $stats['total_count'] }} <span class="text-xs font-normal text-slate-500">Personil</span>
                </div>
                <span class="text-[10px] text-slate-500 mt-1">
                    {{ $stats['published_count'] }} Published · {{ $stats['draft_count'] }} Draft
                </span>
            </div>
        </div>

        <!-- Filter Bar Card (Hidden when printing) -->
        <div
            class="bg-white/95 rounded-3xl border border-slate-200/90 p-5 sm:p-6 shadow-xs backdrop-blur-xs w-full print:hidden">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Parameter Filter Rekapitulasi Slip Gaji</h3>
                </div>
                @if (request()->hasAny(['month', 'year', 'user_id', 'employment_type']))
                    <a href="{{ route('reports.payrolls') }}"
                        class="text-xs text-sky-600 hover:text-sky-700 font-semibold transition">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.payrolls') }}" class="w-full space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Bulan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bulan
                            Penggajian</label>
                        <select name="month"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tahun</label>
                        <select name="year"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
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

                    <!-- Ikatan Kerja -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ikatan
                            Kerja</label>
                        <select name="employment_type"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Ikatan Kerja —</option>
                            <option value="KARTAP" {{ $employmentType === 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap
                                (KARTAP)</option>
                            <option value="PKWT" {{ $employmentType === 'PKWT' ? 'selected' : '' }}>Kontrak Kerja (PKWT)
                            </option>
                            <option value="INTERN" {{ $employmentType === 'INTERN' ? 'selected' : '' }}>Magang (INTERN)
                            </option>
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

        <!-- Payroll Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs w-full">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Slip Penggajian Karyawan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Periode: <strong
                            class="text-slate-700">{{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</strong>
                        · Jadwal Gajian: <strong class="text-indigo-600">Tgl
                            {{ \App\Models\Setting::getValue('payday_date', 25) }} setiap bulan</strong>
                        · Kebijakan WFH: KARTAP {{ \App\Models\Setting::getValue('wfh_deduction_kartap_pct', 10) }}% / PKWT
                        {{ \App\Models\Setting::getValue('wfh_deduction_pkwt_pct', 30) }}%
                    </p>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('reports.payrolls.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-xs font-semibold shadow-2xs hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export ke Excel</span>
                    </a>
                    <span
                        class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                        Total: Rp{{ number_format($stats['total_net'], 0, ',', '.') }}
                    </span>
                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ $payrolls->total() }} Slip
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Karyawan & Status</th>
                            <th class="px-6 py-4 text-center">Rekap Hari</th>
                            <th class="px-6 py-4 text-right">Gaji Pokok</th>
                            <th class="px-6 py-4 text-right">Potongan (WFH & Sakit/Izin)</th>
                            <th class="px-6 py-4 text-right">Lembur</th>
                            <th class="px-6 py-4 text-right">Gaji Bersih (Net)</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Slip Gaji</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payrolls as $p)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- Karyawan -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-xs">{{ $p->user->name ?? 'User Terhapus' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono flex items-center gap-2 mt-0.5">
                                        <span>{{ $p->user->email ?? '-' }}</span>
                                        <span
                                            class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $p->user->employment_type ?? 'KARTAP' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Rekap Hari -->
                                <td class="px-6 py-4 text-center text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <div class="text-[11px] text-slate-400 font-sans italic">Magang Fleksibel</div>
                                    @else
                                        <div class="font-mono text-slate-800">
                                            <span class="font-bold text-emerald-600">{{ $p->total_present_onsite }}d
                                                On-Site</span>
                                            <span class="text-slate-300 mx-1">·</span>
                                            <span class="font-bold text-sky-600">{{ $p->total_wfh_days }}d WFH</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                            Total {{ $p->total_working_days }} hari kerja
                                            @if (($p->total_sick_days + $p->total_permit_days) > 0)
                                                · <span class="text-rose-600 font-bold">{{ $p->total_sick_days + $p->total_permit_days }}d Sakit/Izin</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Gaji Pokok -->
                                <td class="px-6 py-4 text-right font-mono text-xs text-slate-700">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        @if (($p->user->basic_salary ?? 0) > 0)
                                            <div>Rp{{ number_format($p->user->basic_salary, 0, ',', '.') }}</div>
                                            <div class="text-[10px] text-sky-600 font-sans">Uang Saku</div>
                                        @else
                                            <div class="text-slate-400 text-xs italic font-sans"> (Rp0)</div>
                                        @endif
                                    @else
                                        Rp{{ number_format($p->gross_salary, 0, ',', '.') }}
                                    @endif
                                </td>

                                <!-- Potongan WFH & Sakit/Izin -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <span class="text-slate-400 italic font-sans text-[11px]">—</span>
                                    @elseif (($p->wfh_deduction_amount + ($p->leave_deduction_amount ?? 0)) > 0)
                                        <span
                                            class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                            -Rp{{ number_format($p->wfh_deduction_amount + ($p->leave_deduction_amount ?? 0), 0, ',', '.') }}
                                        </span>
                                        @if (($p->leave_deduction_amount ?? 0) > 0)
                                            <div class="text-[10px] text-rose-500 font-sans mt-0.5">
                                                (Skt/Izn: -Rp{{ number_format($p->leave_deduction_amount, 0, ',', '.') }})
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">Rp0</span>
                                    @endif
                                </td>

                                <!-- Uang Lembur / Insentif Project -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        @if ($p->overtime_pay > 0)
                                            <span
                                                class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                +Rp{{ number_format($p->overtime_pay, 0, ',', '.') }}
                                            </span>
                                            <div class="text-[10px] text-emerald-600 font-sans mt-0.5">Insentif Proyek
                                            </div>
                                        @else
                                            <span class="text-slate-300">Rp0</span>
                                        @endif
                                    @elseif ($p->overtime_pay > 0)
                                        <span
                                            class="text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            +Rp{{ number_format($p->overtime_pay, 0, ',', '.') }}
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">({{ $p->total_overtime_hours }}
                                            jam)</div>
                                    @else
                                        <span class="text-slate-300">Rp0</span>
                                    @endif
                                </td>

                                <!-- Gaji Bersih -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    <div class="font-extrabold text-slate-900 text-sm">
                                        Rp{{ number_format($p->net_salary, 0, ',', '.') }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center">
                                    @if ($p->status === 'PUBLISHED')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Published
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>

                                <!-- Slip Gaji Actions -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            onclick="openSlipModal({
                                                id: '{{ $p->id }}',
                                                docNumber: 'CHR-PAY/{{ $p->year }}{{ sprintf('%02d', $p->month) }}/{{ strtoupper(substr($p->id, 0, 8)) }}',
                                                userName: '{{ addslashes($p->user->name ?? 'User') }}',
                                                userEmail: '{{ addslashes($p->user->email ?? '-') }}',
                                                employmentType: '{{ $p->user->employment_type ?? 'KARTAP' }}',
                                                bankAccount: '{{ addslashes($p->user->bank_name ? ($p->user->bank_name . ' - ' . $p->user->bank_account_number) : 'Tunai / Belum Terdaftar') }}',
                                                period: '{{ \Carbon\Carbon::create($p->year, $p->month, 1)->translatedFormat('F Y') }}',
                                                dailyRate: 'Rp {{ number_format($p->daily_salary_rate, 0, ',', '.') }}/hr',
                                                workingDays: '{{ $p->total_working_days }}',
                                                onsiteDays: '{{ $p->total_present_onsite }}',
                                                wfhDays: '{{ $p->total_wfh_days }}',
                                                sickDays: '{{ $p->total_sick_days ?? 0 }}',
                                                permitDays: '{{ $p->total_permit_days ?? 0 }}',
                                                overtimeHours: '{{ $p->total_overtime_hours }}',
                                                basicSalary: 'Rp {{ number_format(($p->user->employment_type ?? '') === 'INTERN' ? ($p->user->basic_salary ?? 0) : ($p->gross_salary - $p->overtime_pay), 0, ',', '.') }}',
                                                overtimePay: 'Rp {{ number_format($p->overtime_pay, 0, ',', '.') }}',
                                                grossSalary: 'Rp {{ number_format($p->gross_salary, 0, ',', '.') }}',
                                                wfhDeduction: 'Rp {{ number_format($p->wfh_deduction_amount, 0, ',', '.') }}',
                                                sickDeduction: 'Rp {{ number_format(($p->total_sick_days ?? 0) * $p->daily_salary_rate, 0, ',', '.') }}',
                                                permitDeduction: 'Rp {{ number_format(($p->total_permit_days ?? 0) * $p->daily_salary_rate, 0, ',', '.') }}',
                                                leaveDeduction: 'Rp {{ number_format($p->leave_deduction_amount ?? 0, 0, ',', '.') }}',
                                                totalDeduction: 'Rp {{ number_format($p->wfh_deduction_amount + ($p->leave_deduction_amount ?? 0), 0, ',', '.') }}',
                                                netSalary: 'Rp {{ number_format($p->net_salary, 0, ',', '.') }}',
                                                terbilang: '{{ addslashes(\App\Helpers\NumberHelper::terbilang($p->net_salary)) }}',
                                                status: '{{ $p->status }}',
                                                pdfUrl: '{{ route('reports.payrolls.pdf', $p->id) }}'
                                            })"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 transition cursor-pointer"
                                            title="Lihat Rincian Slip Lengkap">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Rincian</span>
                                        </button>

                                        <a href="{{ route('reports.payrolls.pdf', $p->id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition shadow-2xs"
                                            title="Unduh Berkas PDF">
                                            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data slip gaji pada periode yang dipilih. Silakan generate payroll di menu
                                    Finance atau ubah filter periode.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payrolls->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>

        @include('partials.slip-modal')

    </div>
@endsection
