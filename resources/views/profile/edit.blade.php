@extends('layouts.app')

@section('title', 'Detail & Edit Profil — ChronoHR')
@section('page_title', 'Profil Pengguna & Pengaturan Akun')

@section('content')
<div class="w-full space-y-8">

    <!-- Profile Identity Hero Card -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white border border-slate-800 shadow-sm relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 border border-slate-700/80 text-sky-400 font-mono font-bold text-xl flex items-center justify-center shrink-0 shadow-inner">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-sky-500/20 text-sky-300 border border-sky-400/30">
                            {{ $user->employment_type }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 font-mono">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-white/10 text-slate-200 border border-slate-700">
                                {{ $role->display_name ?? $role->name }}
                            </span>
                        @endforeach
                        @if($user->can_approve_leaves)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                Otoritas Approver
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hardware Card UID -->
            <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800 font-mono text-xs shrink-0 space-y-1">
                <div class="text-[10px] uppercase tracking-wider text-slate-400">Kartu RFID NodeMCU</div>
                @if($user->rfid_uid)
                    <div class="font-bold text-sky-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>UID: {{ $user->rfid_uid }}</span>
                    </div>
                @else
                    <div class="text-amber-400 font-bold">Belum Dipairing</div>
                @endif
            </div>
        </div>

        <div class="absolute -right-16 -top-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Edit Profile Form Card -->
    <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
        <div class="border-b border-slate-100 pb-5 mb-6">
            <h3 class="text-base font-bold text-slate-900 tracking-tight">Formulir Pembaruan Data Profil</h3>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi identitas, nomor rekening transfer payroll, atau ganti kata sandi login.</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Informasi Dasar -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Informasi Akun</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('name')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Perusahaan</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('email')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Rekening Bank Penggajian (Payroll) -->
            <div class="pt-5 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Rekening Bank Penggajian (Transfer Gaji)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="BCA / Mandiri / BRI / BNI"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('bank_name')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" placeholder="1234567890"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 font-mono text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('bank_account_number')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Atas Nama (Pemilik)</label>
                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $user->bank_account_holder) }}" placeholder="Sesuai buku tabungan"
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        @error('bank_account_holder')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Keamanan & Kata Sandi -->
            <div class="pt-5 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Keamanan Akun & Kata Sandi</h4>
                <div class="max-w-xl">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"
                        class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Minimal 6 karakter jika ingin mengganti kata sandi login.</p>
                    @error('password')<p class="text-rose-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 active:scale-[0.99] text-white rounded-xl text-sm font-semibold shadow-xs hover:shadow transition-all cursor-pointer">
                    Simpan Pembaruan Profil
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
