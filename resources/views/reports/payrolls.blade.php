@extends('layouts.app')

@section('title', 'Laporan Slip Gaji — ChronoHR')
@section('page_title', 'Laporan Rekapitulasi Slip Gaji')
@section('page_subtitle', 'Rekapitulasi beban penggajian bulanan, pemotongan WFH berdasar ikatan kerja, dan insentif
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

            <!-- Total WFH Deduction -->
            <div class="bg-white/95 p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Total Potongan WFH</span>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-600 font-mono mt-2">
                    -Rp{{ number_format($stats['total_wfh_deduction'], 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-slate-500 mt-1">Efisiensi operasional remote</span>
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
                @if (request()->hasAny(['month', 'year', 'user_id', 'status', 'employment_type']))
                    <a href="{{ route('reports.payrolls') }}"
                        class="text-xs text-sky-600 hover:text-sky-700 font-semibold transition">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('reports.payrolls') }}" class="w-full space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
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

                    <!-- Status Slip -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status
                            Slip</label>
                        <select name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition">
                            <option value="">— Semua Status —</option>
                            <option value="PUBLISHED" {{ $status === 'PUBLISHED' ? 'selected' : '' }}>Published (Resmi)
                            </option>
                            <option value="DRAFT" {{ $status === 'DRAFT' ? 'selected' : '' }}>Draft (Review)</option>
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
                        · Kebijakan WFH: KARTAP {{ \App\Models\Setting::getValue('wfh_deduction_kartap_pct', 10) }}% / PKWT
                        {{ \App\Models\Setting::getValue('wfh_deduction_pkwt_pct', 30) }}%
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                        Total: Rp{{ number_format($stats['total_net'], 0, ',', '.') }}
                    </span>
                    <span
                        class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200">
                        {{ $payrolls->count() }} Slip
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
                            <th class="px-6 py-4 text-right">Potongan WFH</th>
                            <th class="px-6 py-4 text-right">Lembur</th>
                            <th class="px-6 py-4 text-right">Gaji Bersih (Net)</th>
                            <th class="px-6 py-4 text-center">Status</th>
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
                                    <div class="font-mono text-slate-800">
                                        <span class="font-bold text-emerald-600">{{ $p->total_present_onsite }}d
                                            On-Site</span>
                                        <span class="text-slate-300 mx-1">·</span>
                                        <span class="font-bold text-sky-600">{{ $p->total_wfh_days }}d WFH</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                        Total {{ $p->total_working_days }} hari kerja
                                    </div>
                                </td>

                                <!-- Gaji Pokok -->
                                <td class="px-6 py-4 text-right font-mono text-xs text-slate-700">
                                    Rp{{ number_format($p->gross_salary, 0, ',', '.') }}
                                </td>

                                <!-- Potongan WFH -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    @if ($p->wfh_deduction_amount > 0)
                                        <span
                                            class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                            -Rp{{ number_format($p->wfh_deduction_amount, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">Rp0</span>
                                    @endif
                                </td>

                                <!-- Uang Lembur -->
                                <td class="px-6 py-4 text-right font-mono text-xs">
                                    @if ($p->overtime_pay > 0)
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data slip gaji pada periode yang dipilih. Silakan generate payroll di menu
                                    Finance atau ubah filter periode.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
