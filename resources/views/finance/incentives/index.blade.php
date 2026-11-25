@extends('layouts.app')

@section('title', 'Insentif Proyek & Magang - ChronoHR')
@section('page_title', 'Pengelolaan Insentif Proyek & Magang')
@section('page_subtitle', 'Catat honorarium proyek bagi peserta magang (INTERN) maupun bonus tambahan proyek bagi karyawan reguler (KARTAP & PKWT).')

@section('content')
    <div class="space-y-6">

        <!-- 3 Kartu Ringkasan Insentif Proyek -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pencairan Insentif</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    Rp {{ number_format($stats['total_amount'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">{{ $stats['total_count'] ?? $incentives->total() }} pencairan tercatat</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-sky-700">Honorarium Peserta Magang</span>
                <div class="text-2xl font-extrabold text-sky-700 font-mono mt-2">
                    Rp {{ number_format($stats['intern_amount'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Apresiasi penyelesaian proyek magang</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Bonus Proyek Karyawan Reguler</span>
                <div class="text-2xl font-extrabold text-emerald-700 font-mono mt-2">
                    Rp {{ number_format($stats['regular_amount'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Bonus proyek KARTAP & PKWT</span>
            </div>
        </div>

        <!-- Form Pencatatan Insentif Proyek Baru -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs w-full">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Catat Insentif Proyek Baru</h2>
                <p class="text-xs text-slate-600 mt-0.5">
                    Insentif yang dicatat akan otomatis diakumulasikan ke dalam perhitungan slip gaji bulanan penerima (baik peserta magang maupun karyawan reguler).
                </p>
            </div>

            <form action="{{ route('finance.incentives.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="inc_store_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Penerima Insentif
                        </label>
                        <select id="inc_store_user_id" name="user_id" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <option value="">Pilih Magang / Karyawan...</option>
                            @foreach ($employees ?? $interns as $person)
                                <option value="{{ $person->id }}">
                                    {{ $person->name }} ({{ $person->employment_type === 'INTERN' ? 'MAGANG' : $person->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="inc_store_project_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Proyek / Tugas Khusus
                        </label>
                        <input type="text" id="inc_store_project_name" name="project_name" placeholder="Contoh: Modul Integrasi IoT RFID" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 transition">
                    </div>

                    <div>
                        <label for="inc_store_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Insentif (Rp)
                        </label>
                        <input type="number" id="inc_store_amount" name="amount" min="1000" step="1000" placeholder="Contoh: 1500000" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-xs font-mono font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 transition">
                    </div>

                    <div>
                        <label for="inc_store_given_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Penyerahan
                        </label>
                        <input type="date" id="inc_store_given_date" name="given_date" value="{{ now()->toDateString() }}" required
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 transition">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-1">
                    <div class="flex-1">
                        <label for="inc_store_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Kinerja / Keterangan Proyek (Opsional)
                        </label>
                        <input type="text" id="inc_store_notes" name="notes" placeholder="Catatan tahapan penyelesaian proyek..."
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 transition">
                    </div>

                    <button type="submit"
                        class="h-[42px] px-6 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer shrink-0">
                        Simpan Insentif Proyek
                    </button>
                </div>
            </form>
        </div>

        <!-- Kartu Filter Riwayat Insentif -->
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
                        <h3 class="text-sm font-bold text-slate-900">Filter Riwayat Insentif Proyek</h3>
                        <p class="text-xs text-slate-600">Saring riwayat pencairan insentif berdasarkan penerima, ikatan kerja, atau periode bulan.</p>
                    </div>
                </div>
                @if (request()->hasAny(['user_id', 'employment_type', 'month', 'year']))
                    <a href="{{ route('finance.incentives.index') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('finance.incentives.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="inc_filter_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Penerima
                        </label>
                        <select id="inc_filter_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <option value="">Semua Penerima</option>
                            @foreach ($employees ?? $interns as $person)
                                <option value="{{ $person->id }}" {{ ($userId ?? '') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} ({{ $person->employment_type === 'INTERN' ? 'MAGANG' : $person->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="inc_filter_emp_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ikatan Kerja
                        </label>
                        <select id="inc_filter_emp_type" name="employment_type"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <option value="">Semua Ikatan Kerja</option>
                            <option value="INTERN" {{ ($employmentType ?? '') === 'INTERN' ? 'selected' : '' }}>Magang / PKL</option>
                            <option value="KARTAP" {{ ($employmentType ?? '') === 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap (KARTAP)</option>
                            <option value="PKWT" {{ ($employmentType ?? '') === 'PKWT' ? 'selected' : '' }}>Kontrak Kerja (PKWT)</option>
                        </select>
                    </div>

                    <div>
                        <label for="inc_filter_month" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Bulan Penyerahan
                        </label>
                        <select id="inc_filter_month" name="month"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <option value="">Semua Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="inc_filter_year" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tahun
                        </label>
                        <select id="inc_filter_year" name="year"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600">
                            <option value="">Semua Tahun</option>
                            @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
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

        <!-- Tabel Riwayat Insentif Proyek -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Riwayat Catatan Insentif Proyek</h2>
                    <p class="text-xs text-slate-600 mt-0.5">Daftar insentif dan bonus proyek yang telah dicatat oleh divisi Keuangan.</p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    {{ $incentives->total() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Penerima Insentif</th>
                            <th class="px-4 py-3.5">Ikatan Kerja</th>
                            <th class="px-4 py-3.5">Nama Proyek</th>
                            <th class="px-4 py-3.5">Tanggal Penyerahan</th>
                            <th class="px-4 py-3.5">Nominal Insentif</th>
                            <th class="px-5 py-3.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($incentives as $inc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $inc->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $inc->user->email ?? '' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if (($inc->user->employment_type ?? '') === 'INTERN')
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-sky-50 text-sky-900 border border-sky-300">
                                            MAGANG
                                        </span>
                                    @elseif (($inc->user->employment_type ?? '') === 'KARTAP')
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                            KARTAP
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                            {{ $inc->user->employment_type ?? '-' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-semibold text-slate-800">{{ $inc->project_name }}</td>
                                <td class="px-4 py-4 font-mono text-xs">{{ $inc->given_date }}</td>
                                <td class="px-4 py-4 font-mono text-xs font-extrabold text-emerald-700">
                                    Rp {{ number_format($inc->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-600">{{ $inc->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    Belum ada data insentif proyek yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $incentives])
        </div>

    </div>
@endsection
