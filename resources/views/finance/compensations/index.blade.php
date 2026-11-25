@extends('layouts.app')

@section('title', 'Data Gaji & Rekening Karyawan - ChronoHR')
@section('page_title', 'Manajemen Gaji Pokok & Rekening Karyawan')
@section('page_subtitle', 'Kelola besaran gaji pokok bulanan (KARTAP & PKWT), uang saku magang, serta verifikasi nomor rekening tujuan transfer gaji.')

@section('content')
    <div class="space-y-6">

        <!-- 4 Kartu Ringkasan Finansial Karyawan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pegawai Terdata</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    {{ $stats['total_employees'] }} <span class="text-xs font-normal text-slate-500">Personil</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">KARTAP, PKWT & Magang</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Komitmen Gaji Pokok</span>
                <div class="text-2xl font-extrabold text-slate-900 font-mono mt-2">
                    Rp {{ number_format($stats['total_basic_salary'], 0, ',', '.') }}
                </div>
                <span class="text-xs text-slate-500 mt-1">Akumulasi gaji pokok KARTAP & PKWT</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Rekening Terverifikasi</span>
                <div class="text-2xl font-extrabold text-emerald-700 font-mono mt-2">
                    {{ $stats['bank_registered'] }} <span class="text-xs font-normal text-slate-500">Rekening</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">Siap untuk pencairan transfer bank</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Belum Ada Rekening</span>
                <div class="text-2xl font-extrabold text-amber-700 font-mono mt-2">
                    {{ $stats['bank_unregistered'] }} <span class="text-xs font-normal text-slate-500">Personil</span>
                </div>
                <span class="text-xs text-slate-500 mt-1">Perlu pendataan nomor rekening</span>
            </div>
        </div>

        <!-- Kartu Filter Data Gaji & Rekening -->
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
                        <h3 class="text-sm font-bold text-slate-900">Filter Data Kompensasi & Rekening Pegawai</h3>
                        <p class="text-xs text-slate-600">Saring daftar pegawai berdasarkan nama personil, jenis ikatan kerja, atau kelengkapan rekening bank.</p>
                    </div>
                </div>
                @if (request()->hasAny(['user_id', 'employment_type', 'bank_status']))
                    <a href="{{ route('finance.compensations.index') }}"
                        class="text-xs text-sky-700 hover:text-sky-900 font-bold underline transition">
                        Atur Ulang Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('finance.compensations.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <!-- Personil Karyawan -->
                    <div>
                        <label for="comp_filter_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Personil Karyawan
                        </label>
                        <select id="comp_filter_user_id" name="user_id"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Personil</option>
                            @foreach ($allUsers as $u)
                                <option value="{{ $u->id }}" {{ ($userId ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->employment_type === 'INTERN' ? 'MAGANG' : $u->employment_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ikatan Kerja -->
                    <div>
                        <label for="comp_filter_employment_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ikatan Kerja
                        </label>
                        <select id="comp_filter_employment_type" name="employment_type"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Ikatan Kerja</option>
                            <option value="KARTAP" {{ ($employmentType ?? '') === 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap (KARTAP)</option>
                            <option value="PKWT" {{ ($employmentType ?? '') === 'PKWT' ? 'selected' : '' }}>Kontrak Kerja (PKWT)</option>
                            <option value="INTERN" {{ ($employmentType ?? '') === 'INTERN' ? 'selected' : '' }}>Magang / PKL</option>
                        </select>
                    </div>

                    <!-- Status Rekening -->
                    <div>
                        <label for="comp_filter_bank_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kelengkapan Rekening
                        </label>
                        <select id="comp_filter_bank_status" name="bank_status"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition">
                            <option value="">Semua Status Rekening</option>
                            <option value="REGISTERED" {{ ($bankStatus ?? '') === 'REGISTERED' ? 'selected' : '' }}>Rekening Terdaftar</option>
                            <option value="UNREGISTERED" {{ ($bankStatus ?? '') === 'UNREGISTERED' ? 'selected' : '' }}>Belum Ada Rekening</option>
                        </select>
                    </div>

                    <!-- Tombol Terapkan -->
                    <div>
                        <button type="submit"
                            class="w-full h-[42px] px-5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-sky-600">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Data Gaji Pokok & Rekening Karyawan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Daftar Gaji Pokok & Rekening Transfer Pegawai</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Klik tombol <strong>Atur Gaji & Rekening</strong> untuk memperbarui nominal gaji pokok bulanan atau informasi rekening bank pegawai.
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto shrink-0">
                    {{ $employees->total() }} Pegawai
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Nama & Email Pegawai</th>
                            <th class="px-4 py-3.5">Ikatan Kerja</th>
                            <th class="px-4 py-3.5">Gaji Pokok Bulanan</th>
                            <th class="px-4 py-3.5">Acuan Tarif Harian (22 Hr)</th>
                            <th class="px-4 py-3.5">Nama Bank & Nomor Rekening</th>
                            <th class="px-4 py-3.5">Nama Pemilik Rekening</th>
                            <th class="px-5 py-3.5 text-right">Aksi Keuangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employees as $emp)
                            @php
                                $dailyEst = $emp->employment_type === 'INTERN' ? 0 : (($emp->basic_salary ?? 0) / 22);
                                $compPayload = [
                                    'updateUrl' => route('finance.compensations.update', $emp->id),
                                    'name' => $emp->name,
                                    'email' => $emp->email,
                                    'employmentType' => $emp->employment_type,
                                    'basicSalary' => (float) ($emp->basic_salary ?? 0),
                                    'bankName' => $emp->bank_name ?? '',
                                    'bankAccountNumber' => $emp->bank_account_number ?? '',
                                    'bankAccountHolder' => $emp->bank_account_holder ?? $emp->name,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $emp->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $emp->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if ($emp->employment_type === 'KARTAP')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                            KARTAP
                                        </span>
                                    @elseif ($emp->employment_type === 'PKWT')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                            PKWT
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-sky-50 text-sky-900 border border-sky-300">
                                            MAGANG
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-mono text-xs font-extrabold text-slate-900">
                                    @if ($emp->employment_type === 'INTERN' && ($emp->basic_salary ?? 0) == 0)
                                        <span class="text-slate-500 font-normal">Rp 0 (Skema Insentif)</span>
                                    @else
                                        Rp {{ number_format($emp->basic_salary ?? 0, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-mono text-xs text-slate-600">
                                    @if ($emp->employment_type === 'INTERN')
                                        <span class="text-slate-400">Non-Harian</span>
                                    @else
                                        Rp {{ number_format($dailyEst, 0, ',', '.') }} / hr
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs">
                                    @if (!empty($emp->bank_account_number))
                                        <div class="font-bold text-slate-900">{{ $emp->bank_name ?: 'Bank Umum' }}</div>
                                        <div class="font-mono text-slate-600">{{ $emp->bank_account_number }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Belum Terdaftar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs font-semibold text-slate-700">
                                    {{ $emp->bank_account_holder ?: '-' }}
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <button type="button"
                                        onclick='openCompensationModal(@json($compPayload))'
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white hover:bg-slate-800 transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Atur Gaji & Rekening</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    Tidak ada data pegawai yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $employees])
        </div>
    </div>

    <!-- MODAL PENGATURAN GAJI POKOK & REKENING BANK -->
    <div id="compensationModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="comp_modal_title"
        onclick="if(event.target === this) closeCompensationModal()"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 id="comp_modal_title" class="text-base font-bold text-slate-900">Perbarui Gaji Pokok & Rekening</h3>
                    <p class="text-xs text-slate-600 mt-0.5" id="comp_modal_subtitle">-</p>
                </div>
                <button type="button" onclick="closeCompensationModal()"
                    aria-label="Tutup modal"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-200/60 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="comp_modal_form" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="comp_basic_salary" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="comp_salary_label">
                        Gaji Pokok Bulanan (Rp)
                    </label>
                    <input type="number" id="comp_basic_salary" name="basic_salary" min="0" step="1000" required
                        class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm font-mono font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900">
                    <p class="text-[11px] text-slate-500 mt-1" id="comp_salary_hint">
                        Digunakan sebagai dasar perhitungan tarif harian, potongan WFA, dan slip gaji bulanan.
                    </p>
                </div>

                <div class="pt-2 border-t border-slate-100 space-y-4">
                    <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Informasi Rekening Tujuan Transfer
                    </div>

                    <div>
                        <label for="comp_bank_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Bank
                        </label>
                        <input type="text" id="comp_bank_name" name="bank_name" placeholder="Contoh: BCA, Mandiri, BRI, BNI, BSI"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900">
                    </div>

                    <div>
                        <label for="comp_bank_account_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Rekening
                        </label>
                        <input type="text" id="comp_bank_account_number" name="bank_account_number" placeholder="Contoh: 1234567890"
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900">
                    </div>

                    <div>
                        <label for="comp_bank_account_holder" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Pemilik Rekening
                        </label>
                        <input type="text" id="comp_bank_account_holder" name="bank_account_holder" placeholder="Nama sesuai buku tabungan..."
                            class="w-full h-[42px] px-3.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs font-semibold focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeCompensationModal()"
                        class="h-[40px] px-4 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="h-[40px] px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition cursor-pointer shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCompensationModal(data) {
            document.getElementById('comp_modal_form').action = data.updateUrl;
            document.getElementById('comp_modal_subtitle').textContent = data.name + ' (' + data.email + ') - ' + (data.employmentType === 'INTERN' ? 'MAGANG' : data.employmentType);
            document.getElementById('comp_basic_salary').value = data.basicSalary || 0;
            document.getElementById('comp_bank_name').value = data.bankName || '';
            document.getElementById('comp_bank_account_number').value = data.bankAccountNumber || '';
            document.getElementById('comp_bank_account_holder').value = data.bankAccountHolder || data.name || '';

            if (data.employmentType === 'INTERN') {
                document.getElementById('comp_salary_label').textContent = 'Uang Saku Magang Bulanan (Rp)';
                document.getElementById('comp_salary_hint').textContent = 'Isi 0 jika peserta magang hanya menerima insentif berbasis proyek.';
            } else {
                document.getElementById('comp_salary_label').textContent = 'Gaji Pokok Bulanan (' + data.employmentType + ') (Rp)';
                document.getElementById('comp_salary_hint').textContent = 'Digunakan sebagai dasar perhitungan tarif harian, potongan WFA, dan slip gaji bulanan.';
            }

            document.getElementById('compensationModal').classList.remove('hidden');
        }

        function closeCompensationModal() {
            document.getElementById('compensationModal').classList.add('hidden');
        }
    </script>
@endsection
