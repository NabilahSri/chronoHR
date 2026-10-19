@extends('layouts.app')

@section('title', 'Kalkulasi Payroll — ChronoHR ')
@section('page_title', 'Otomasi Penggajian & Rekap Payroll')

@section('content')
    <div class="space-y-6">

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
                    <div class="hidden sm:block">
                        <input type="number" name="total_working_days" value="22" title="Hari Kerja Efektif Bulanan"
                            class="w-20 px-3 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm font-mono text-center focus:ring-4 focus:ring-slate-900/5">
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
                    <p class="text-xs text-slate-500 mt-0.5">Rumus: Gaji Pokok - Potongan WFH (KARTAP {{ \App\Models\Setting::getValue('wfh_deduction_kartap_pct', 10) }}% / PKWT {{ \App\Models\Setting::getValue('wfh_deduction_pkwt_pct', 30) }}%) +
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
                            <th class="px-6 py-4">Potongan WFH</th>
                            <th class="px-6 py-4">Uang Lembur</th>
                            <th class="px-6 py-4">Gaji Netto</th>
                            <th class="px-6 py-4">Status</th>
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
                                    <span
                                        class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $p->user->employment_type ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono space-y-0.5">
                                    <div>Hadir: <span
                                            class="font-bold text-slate-900">{{ $p->total_present_onsite }}</span> hr</div>
                                    <div>WFH: <span class="font-bold text-sky-700">{{ $p->total_wfh_days }}</span> hr
                                    </div>
                                    <div>Lembur: <span
                                            class="font-bold text-amber-600">{{ $p->total_overtime_hours }}</span> jam
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    <div class="font-bold text-slate-900">Rp
                                        {{ number_format($p->user->basic_salary ?? 0, 0, ',', '.') }}</div>
                                    <div class="text-slate-400 text-[11px]">@ Rp
                                        {{ number_format($p->daily_salary_rate, 0, ',', '.') }}/hr</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-rose-600 font-bold">
                                    - Rp {{ number_format($p->wfh_deduction_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-emerald-600 font-bold">
                                    + Rp {{ number_format($p->overtime_pay, 0, ',', '.') }}
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-400">
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
        </div>
    </div>
@endsection
