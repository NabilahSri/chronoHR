@extends('layouts.app')

@section('title', 'Kalkulasi Payroll — ChronoHR ')
@section('page_title', 'Otomasi Penggajian & Rekap Payroll')

@section('content')
    <div class="space-y-6">

        <!-- Info & Cross-Link Banner -->
        <div class="bg-gradient-to-r from-sky-500/10 via-slate-50 to-indigo-500/10 rounded-2xl border border-sky-200/80 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Modul Operasional Kalkulasi & Publish Payroll</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Gunakan tombol di bawah untuk <strong>hitung draft otomatis</strong> dari presensi atau <strong>publish slip</strong> ke karyawan.
                    </p>
                </div>
            </div>
            <a href="{{ route('reports.payrolls', ['month' => $month, 'year' => $year]) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 rounded-xl text-xs font-bold shadow-2xs transition self-start sm:self-auto shrink-0">
                <span>Buka Laporan & Arsip Slip Gaji</span>
                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Filter & Action Card -->
        <div
            class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 shadow-xs backdrop-blur-xs flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <form method="GET" action="{{ route('finance.payroll.index') }}" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bulan
                        Penggajian</label>
                    <select name="month"
                        class="px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}"
                        class="w-24 px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                </div>
                <div class="self-end">
                    <button type="submit"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-semibold transition cursor-pointer">
                        Filter Periode
                    </button>
                </div>
            </form>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Hitung Ulang Form -->
                <form action="{{ route('finance.payroll.generate') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <div class="flex items-center bg-white border border-slate-300 rounded-xl px-3 py-1.5 shadow-2xs"
                        title="Jumlah Hari Kerja Efektif Bulanan (standar 22 hari). Digunakan sebagai pembagi Gaji Pokok untuk menghitung Tarif Harian.">
                        <label for="total_working_days" class="text-xs font-semibold text-slate-600 mr-2 flex items-center gap-1.5 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Hari Kerja:
                        </label>
                        <input type="number" id="total_working_days" name="total_working_days" value="22" min="1" max="31"
                            class="w-14 px-2 py-1 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm font-bold font-mono text-center shadow-inner focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <span class="text-xs font-medium text-slate-400 ml-1.5">Hari</span>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-500 active:scale-[0.99] text-white rounded-xl text-sm font-semibold shadow-xs hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Kalkulasi Payroll</span>
                    </button>
                </form>

                <!-- Publish Form -->
                @if ($payrolls->isNotEmpty() && !$isPublished)
                    <form action="{{ route('finance.payroll.publish') }}" method="POST"
                        onsubmit="return confirm('Publish slip gaji periode ini ke seluruh personil?')">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-sm font-semibold shadow-xs hover:shadow transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Publish ke Mobile</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Period Status Card -->
        <div
            class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 shadow-xs backdrop-blur-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Beban Payroll Periode
                    Terpilih</span>
                <div class="text-3xl font-extrabold text-slate-900 font-mono mt-1">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
                <p class="text-xs text-slate-400 mt-1">Total kumulatif gaji netto setelah potongan WFH dan tunjangan lembur.
                </p>
            </div>
            <div>
                @if ($payrolls->isEmpty())
                    <span
                        class="px-4 py-1.5 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">BELUM
                        DIKALKULASI</span>
                @elseif($isPublished)
                    <span
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        PUBLISHED (Rilis ke Karyawan)
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-mono font-bold bg-amber-50 text-amber-700 border border-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        DRAFT (Review Finance)
                    </span>
                @endif
            </div>
        </div>

        <!-- Payroll Table Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Slip Penggajian Personil</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Rumus: Gaji Pokok - Potongan WFH (KARTAP
                        {{ \App\Models\Setting::getValue('wfh_deduction_kartap_pct', 10) }}% / PKWT
                        {{ \App\Models\Setting::getValue('wfh_deduction_pkwt_pct', 30) }}%) +
                        Lembur (Rp21rb/jam).</p>
                </div>
                <span
                    class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    {{ $payrolls->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/80 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Karyawan & Rekening</th>
                            <th class="px-6 py-4">Status Kerja</th>
                            <th class="px-6 py-4">Presensi & Jam</th>
                            <th class="px-6 py-4">Gaji Pokok / Rate Harian</th>
                            <th class="px-6 py-4">Potongan (WFH & Sakit/Izin)</th>
                            <th class="px-6 py-4">Uang Lembur</th>
                            <th class="px-6 py-4">Gaji Netto</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Slip Gaji</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($payrolls as $p)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $p->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">
                                        {{ $p->user->bank_name ? $p->user->bank_name . ' - ' . $p->user->bank_account_number : '— Rekening Belum Ada —' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            INTERN (Magang)
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $p->user->employment_type ?? '—' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-mono space-y-0.5">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <div class="text-[11px] text-slate-400 font-sans italic">Presensi magang fleksibel
                                        </div>
                                    @else
                                        <div>Hadir: <span
                                                class="font-bold text-slate-900">{{ $p->total_present_onsite }}</span> hr
                                        </div>
                                        <div>WFH: <span class="font-bold text-sky-700">{{ $p->total_wfh_days }}</span> hr
                                        </div>
                                        @if (($p->total_sick_days + $p->total_permit_days) > 0)
                                            <div>Sakit/Izin: <span
                                                    class="font-bold text-rose-600">{{ $p->total_sick_days + $p->total_permit_days }}</span> hr
                                            </div>
                                        @endif
                                        <div>Lembur: <span
                                                class="font-bold text-amber-600">{{ $p->total_overtime_hours }}</span> jam
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        @if (($p->user->basic_salary ?? 0) > 0)
                                            <div class="font-bold text-slate-900">Rp
                                                {{ number_format($p->user->basic_salary, 0, ',', '.') }}</div>
                                            <div class="text-sky-600 text-[10px] font-sans">Uang Saku Sukarela</div>
                                        @else
                                            <div class="text-slate-400 text-xs italic font-sans">Uang Saku (Rp 0)</div>
                                        @endif
                                    @else
                                        <div class="font-bold text-slate-900">Rp
                                            {{ number_format($p->user->basic_salary ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-slate-400 text-[11px]">@ Rp
                                            {{ number_format($p->daily_salary_rate, 0, ',', '.') }}/hr</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        <span class="text-slate-400 italic font-sans text-[11px]">— (Bebas Potongan)</span>
                                    @else
                                        <div class="text-rose-600 font-bold font-mono">
                                            - Rp {{ number_format($p->wfh_deduction_amount + ($p->leave_deduction_amount ?? 0), 0, ',', '.') }}
                                        </div>
                                        @if (($p->leave_deduction_amount ?? 0) > 0)
                                            <div class="text-[10px] text-rose-500 font-sans mt-0.5">
                                                (Skt/Izn: -Rp{{ number_format($p->leave_deduction_amount, 0, ',', '.') }})
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    @if (($p->user->employment_type ?? '') === 'INTERN')
                                        @if ($p->overtime_pay > 0)
                                            <span class="text-emerald-600 font-bold">
                                                + Rp {{ number_format($p->overtime_pay, 0, ',', '.') }}
                                            </span>
                                            <div class="text-emerald-700 text-[10px] font-sans font-semibold">Bayaran
                                                Proyek</div>
                                        @else
                                            <span class="text-slate-300 font-sans">—</span>
                                        @endif
                                    @else
                                        <span class="text-emerald-600 font-bold">
                                            + Rp {{ number_format($p->overtime_pay, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-sm font-extrabold text-slate-900">
                                    Rp {{ number_format($p->net_salary, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold {{ $p->status === 'PUBLISHED' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
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
                                <td colspan="9" class="px-6 py-16 text-center text-slate-400">
                                    <p class="font-medium text-slate-600">Belum ada kalkulasi payroll untuk periode
                                        {{ $month }}/{{ $year }}</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Kalkulasi Payroll" di bagian atas
                                        untuk memulai generate otomatis.</p>
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
