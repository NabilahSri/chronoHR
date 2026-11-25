@extends('layouts.app')

@section('title', 'Tambah Karyawan - ChronoHR')
@section('page_title', 'Registrasi Karyawan Baru')

@section('content')
    <div class="w-full">
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs backdrop-blur-xs">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Formulir Identitas & Akses Karyawan</h2>
                <p class="text-xs text-slate-500 mt-1">Daftarkan personil baru ke dalam ekosistem ChronoHR untuk sinkronisasi
                    RFID dan portal.</p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nama & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama
                            Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Contoh: Budi Santoso"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email
                            Perusahaan</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="budi@perusahaan.com"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('email')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password & RFID UID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi
                            Akun</label>
                        <input type="password" name="password" placeholder="Bawaan: password"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika ingin kata sandi bawaan: "password".</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="rfid_uid_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                UID Kartu RFID (NodeMCU)
                            </label>
                            <button type="button" onclick="fetchLatestRfidScan()"
                                class="text-[11px] font-bold text-sky-700 hover:text-sky-900 underline cursor-pointer">
                                Ambil dari Tap Alat RFID
                            </button>
                        </div>
                        <input type="text" id="rfid_uid_input" name="rfid_uid" value="{{ old('rfid_uid') }}" placeholder="Contoh: 1234567890"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm font-mono focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        <p id="rfid_scan_hint" class="text-[11px] text-slate-400 mt-1">Klik "Ambil dari Tap Alat RFID" setelah menempelkan kartu baru ke NodeMCU.</p>
                        @error('rfid_uid')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status Kerja & Gaji Pokok -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Ikatan
                            Kerja</label>
                        <select name="employment_type" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                            <option value="KARTAP" {{ old('employment_type') == 'KARTAP' ? 'selected' : '' }}>Karyawan Tetap
                                (KARTAP) - Potongan WFA {{ \App\Models\Setting::getValue('wfa_deduction_kartap_pct', 10) }}%</option>
                            <option value="PKWT" {{ old('employment_type') == 'PKWT' ? 'selected' : '' }}>Kontrak Kerja
                                (PKWT) - Potongan WFA {{ \App\Models\Setting::getValue('wfa_deduction_pkwt_pct', 30) }}%</option>
                            <option value="INTERN" {{ old('employment_type') == 'INTERN' ? 'selected' : '' }}>Magang / PKL
                                - Insentif Proyek</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Gaji Pokok
                            Bulanan (Rp)</label>
                        <input type="number" name="basic_salary" value="{{ old('basic_salary', 0) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('basic_salary')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Roles Checklist -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Penugasan Peran
                        Sistem</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        @foreach ($roles as $role)
                            <label
                                class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-200/90 hover:border-slate-400 bg-slate-50/50 hover:bg-white transition cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    class="mt-0.5 rounded text-sky-600 focus:ring-sky-500">
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ $role->display_name ?? $role->name }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $role->name }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Approver Privilege Switch -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 flex items-start gap-3.5">
                    <input type="checkbox" name="can_approve_leaves" id="can_approve" value="1"
                        {{ old('can_approve_leaves') ? 'checked' : '' }}
                        class="mt-1 rounded text-sky-600 focus:ring-sky-500">
                    <label for="can_approve" class="cursor-pointer">
                        <p class="text-xs font-bold text-slate-900">Otoritas Persetujuan Cuti & Izin (Penyetuju)</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Centang opsi ini jika personil adalah Supervisor, Manajer, atau Atasan langsung yang berhak
                            menyetujui/menolak pengajuan cuti tim melalui portal web.
                        </p>
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                    <a href="{{ route('admin.users.index') }}"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold shadow-sm hover:shadow active:scale-[0.99] transition-all cursor-pointer">
                        Simpan Data Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function fetchLatestRfidScan() {
            const hint = document.getElementById('rfid_scan_hint');
            hint.textContent = 'Mengambil pindaian kartu terakhir dari alat NodeMCU...';
            fetch('{{ route('admin.devices.latest-scan') }}')
                .then(res => res.json())
                .then(data => {
                    if (data.rfid_uid) {
                        document.getElementById('rfid_uid_input').value = data.rfid_uid;
                        hint.textContent = 'Terdeteksi UID: ' + data.rfid_uid + ' dari ' + (data.device || 'NodeMCU') + ' (' + (data.scanned_at || 'baru saja') + ')';
                        hint.className = 'text-[11px] text-emerald-700 font-semibold mt-1';
                    } else {
                        hint.textContent = 'Belum ada kartu RFID yang ditempelkan pada alat NodeMCU.';
                        hint.className = 'text-[11px] text-amber-700 font-semibold mt-1';
                    }
                })
                .catch(() => {
                    hint.textContent = 'Gagal menghubungi server untuk mengambil pindaian RFID.';
                });
        }
    </script>
@endsection
