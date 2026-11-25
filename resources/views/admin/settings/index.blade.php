@extends('layouts.app')

@section('title', 'Pengaturan Sistem - ' . ($appBrandName ?? 'ChronoHR'))
@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Kelola identitas merek aplikasi (logo, nama, slogan), titik acuan lokasi kantor, toleransi jam kerja, kebijakan WFA, dan jadwal penggajian.')

@section('content')
    <div class="space-y-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 0: Identitas & Branding Aplikasi (Logo, Nama Aplikasi, & Slogan) -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-sky-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Identitas & Branding Aplikasi</h2>
                            <p class="text-xs text-slate-500">Sesuaikan logo, nama aplikasi, dan slogan yang tampil pada bilah navigasi, halaman masuk, serta dokumen resmi.</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Aktif: {{ $appName ?? 'ChronoHR' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <!-- Kolom Kiri: Pratinjau & Unggah Logo -->
                    <div class="lg:col-span-5 flex items-start gap-4 bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 shadow-2xs p-2 flex items-center justify-center shrink-0 overflow-hidden">
                            @if (!empty($appLogoPath))
                                <img id="brand-logo-preview-img" src="{{ asset($appLogoPath) }}" alt="{{ $appName ?? 'Logo' }}"
                                    class="w-full h-full object-contain">
                            @else
                                <img id="brand-logo-preview-img" src="" alt="Pratinjau Logo" class="w-full h-full object-contain hidden">
                                <svg id="brand-logo-default-svg" class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="32" cy="32" r="23" stroke="#CBD5E1" stroke-width="1.8" stroke-dasharray="2 3" opacity="0.9" />
                                    <path d="M 32 9 A 23 23 0 0 1 55 32" stroke="#0284C7" stroke-width="2.6" stroke-linecap="round" />
                                    <circle cx="32" cy="9" r="2.2" fill="#0284C7" />
                                    <rect x="15" y="37" width="34" height="15" rx="3.5" fill="#0F172A" stroke="#0284C7" stroke-width="1.2" />
                                    <rect x="23" y="27" width="4.5" height="9" rx="1.5" fill="#0284C7" opacity="0.75" />
                                    <rect x="29.8" y="20.5" width="4.5" height="15.5" rx="1.5" fill="#0284C7" opacity="0.9" />
                                    <rect x="36.5" y="14" width="4.5" height="22" rx="1.5" fill="#0284C7" />
                                    <path d="M 21.5 27.5 L 29.5 35.5 L 45.5 15.5" stroke="#0F172A" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0 space-y-2">
                            <label for="app_logo" class="block text-xs font-semibold text-slate-800">Logo Aplikasi (Opsional)</label>
                            <input type="file" name="app_logo" id="app_logo" accept=".png,.jpg,.jpeg,.webp,.svg"
                                onchange="previewAppLogo(this)"
                                class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:cursor-pointer cursor-pointer">
                            <p class="text-[11px] text-slate-500 leading-tight">
                                Format PNG, JPG, WEBP, atau SVG (Maks. 2MB). Rasio persegi 1:1 disarankan.
                            </p>
                            @if (!empty($appLogoPath))
                                <label class="inline-flex items-center gap-2 pt-1 cursor-pointer">
                                    <input type="checkbox" name="reset_app_logo" value="1"
                                        class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span class="text-[11px] font-semibold text-rose-700">Kembalikan ke logo vektor bawaan</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- Kolom Kanan: Nama Aplikasi & Slogan -->
                    <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="app_name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Aplikasi</label>
                            <input type="text" name="app_name" id="app_name"
                                value="{{ old('app_name', $appName ?? 'ChronoHR') }}" required maxlength="60"
                                placeholder="Contoh: ChronoHR"
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <p class="text-[11px] text-slate-500 mt-1.5">
                                Tampil di sidebar, judul tab browser, halaman login, dan kop dokumen.
                            </p>
                        </div>

                        <div>
                            <label for="app_slogan" class="block text-xs font-semibold text-slate-700 mb-1.5">Slogan / Tagline Aplikasi</label>
                            <input type="text" name="app_slogan" id="app_slogan"
                                value="{{ old('app_slogan', $appSlogan ?? 'Presisi Waktu & Penggajian') }}" required maxlength="120"
                                placeholder="Contoh: Presisi Waktu & Penggajian"
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <p class="text-[11px] text-slate-500 mt-1.5">
                                Sub-judul identitas sistem di bawah nama utama aplikasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: Titik Acuan Lokasi Kantor & Radius Presensi -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Titik Acuan Lokasi Kantor (Radius Lokasi Presensi)</h2>
                            <p class="text-xs text-slate-500">Acuan radius kehadiran untuk Magang, WFA, dan karyawan kantor yang lupa kartu RFID.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="detectCurrentLocation()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 transition cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-sky-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Deteksi GPS Saat Ini</span>
                        </button>
                        <a id="view-map-link"
                            href="https://www.google.com/maps?q={{ $officeLatitude }},{{ $officeLongitude }}"
                            target="_blank"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 transition">
                            <span>Buka di Peta</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Gedung / Kantor</label>
                        <input type="text" name="office_name" id="office_name"
                            value="{{ old('office_name', $officeName) }}"
                            placeholder="Contoh: Kantor Pusat ChronoHR"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Lengkap</label>
                        <input type="text" name="office_address" id="office_address"
                            value="{{ old('office_address', $officeAddress) }}"
                            placeholder="Contoh: Sudirman Central Business District (SCBD)"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-medium focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:col-span-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Garis Lintang (Latitude)</label>
                            <input type="number" step="any" name="office_latitude" id="office_latitude"
                                value="{{ old('office_latitude', $officeLatitude) }}" required
                                class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Garis Bujur (Longitude)</label>
                            <input type="number" step="any" name="office_longitude" id="office_longitude"
                                value="{{ old('office_longitude', $officeLongitude) }}" required
                                class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Radius Toleransi Kantor</label>
                        <div class="relative w-full">
                            <input type="number" name="office_radius_meters" id="office_radius_meters"
                                value="{{ old('office_radius_meters', $officeRadiusMeters) }}" min="10" max="50000" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Meter</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500">
                    <span id="gps-status-text">Titik koordinat acuan kantor aktif tersimpan.</span>
                </div>
            </div>

            <!-- SECTION 2: Jam Kerja & Toleransi Waktu -->
            <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Jam Kerja, Toleransi Waktu & Ambang Lembur</h2>
                        <p class="text-xs text-slate-500">Patokan jam masuk dan jam pulang dinamis untuk menghitung keterlambatan serta durasi lembur otomatis.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="work_start_time" class="block text-xs font-semibold text-slate-700 mb-1.5">Jam Masuk Standar (WIB)</label>
                        <input type="time" name="work_start_time" id="work_start_time"
                            value="{{ old('work_start_time', $workStartTime ?? '08:00') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        <p class="text-[11px] text-slate-500 mt-1">Acuan awal penghitungan keterlambatan check-in.</p>
                    </div>

                    <div>
                        <label for="work_end_time" class="block text-xs font-semibold text-slate-700 mb-1.5">Jam Pulang Standar (WIB)</label>
                        <input type="time" name="work_end_time" id="work_end_time"
                            value="{{ old('work_end_time', $workEndTime ?? '17:00') }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        <p class="text-[11px] text-slate-500 mt-1">Acuan awal penghitungan jam lembur saat checkout.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Toleransi Keterlambatan Masuk</label>
                        <div class="relative w-full">
                            <input type="number" name="late_tolerance_minutes"
                                value="{{ old('late_tolerance_minutes', $lateTolerance) }}" min="0" max="120" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit (setelah {{ $workStartTime ?? '08:00' }})</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ambang Mulai Lembur Pulang</label>
                        <div class="relative w-full">
                            <input type="number" name="overtime_tolerance_minutes"
                                value="{{ old('overtime_tolerance_minutes', $overtimeTolerance) }}" min="0" max="180" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit (setelah {{ $workEndTime ?? '17:00' }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Kebijakan Potongan WFA & Penggajian (2 Kolom Bersandingan) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Potongan WFA -->
                <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Kebijakan Potongan WFA</h2>
                                <p class="text-xs text-slate-500">Persentase potongan gaji harian per hari WFA.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Karyawan Tetap (KARTAP)</label>
                                <div class="relative w-full">
                                    <input type="number" name="wfa_deduction_kartap_pct"
                                        value="{{ old('wfa_deduction_kartap_pct', $wfaDeductionKartap) }}" min="0" max="100" step="0.5" required
                                        class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Karyawan Kontrak (PKWT)</label>
                                <div class="relative w-full">
                                    <input type="number" name="wfa_deduction_pkwt_pct"
                                        value="{{ old('wfa_deduction_pkwt_pct', $wfaDeductionPkwt) }}" min="0" max="100" step="0.5" required
                                        class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Tanggal Gajian -->
                <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 pb-3 mb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Jadwal Tanggal Gajian</h2>
                                <p class="text-xs text-slate-500">Tanggal tutup buku dan rilis slip gaji bulanan.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Gajian (Terbit Slip Gaji)</label>
                            <div class="relative w-full">
                                <input type="number" name="payday_date"
                                    value="{{ old('payday_date', $paydayDate) }}" min="1" max="31" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Setiap Bulan</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2">
                                Slip gaji diterbitkan otomatis dan dapat diakses personil per tanggal <strong>{{ $paydayDate }}</strong> setiap bulan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end pt-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition cursor-pointer active:scale-95">
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>

        <!-- SECTION 4: Kalender Hari Libur Nasional & Cuti Bersama -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Kalender Hari Libur Nasional & Cuti Bersama ({{ $holidayYear ?? now()->year }})</h2>
                        <p class="text-xs text-slate-500">Digunakan untuk menghitung <strong>Hari Kerja Efektif Otomatis</strong> (Senin s/d Jumat dikurangi hari libur) pada Kalkulasi Penggajian.</p>
                    </div>
                </div>
                <form action="{{ route('admin.settings.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="holiday_year" onchange="this.form.submit()"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800">
                        @for ($y = now()->year - 1; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ ($holidayYear ?? now()->year) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <!-- Form Tambah Hari Libur -->
            <form action="{{ route('admin.settings.holidays.store') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-12 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 items-end">
                @csrf
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Libur</label>
                    <input type="date" name="date" required value="{{ now()->toDateString() }}"
                        class="w-full h-[40px] px-3 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Keterangan / Nama Hari Libur</label>
                    <input type="text" name="name" required placeholder="Contoh: Libur Nasional / Cuti Bersama..."
                        class="w-full h-[40px] px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-900">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Libur</label>
                    <select name="type" required
                        class="w-full h-[40px] px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900">
                        <option value="NASIONAL">Libur Nasional</option>
                        <option value="CUTI_BERSAMA">Cuti Bersama</option>
                        <option value="INTERNAL">Libur Internal Perusahaan</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                        class="w-full h-[40px] px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                        + Tambah Libur
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Hari Libur -->
            @if (isset($holidays))
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Hari</th>
                                <th class="px-4 py-3">Keterangan Hari Libur</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($holidays as $holiday)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $holiday->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-600">
                                        {{ $holiday->date->translatedFormat('l') }}
                                        @if ($holiday->date->isWeekend())
                                            <span class="ml-1 text-[10px] font-mono text-slate-400">(Akhir Pekan)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $holiday->name }}</td>
                                    <td class="px-4 py-3">
                                        @if ($holiday->type === 'NASIONAL')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">LIBUR NASIONAL</span>
                                        @elseif ($holiday->type === 'CUTI_BERSAMA')
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">CUTI BERSAMA</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-sky-50 text-sky-800 border border-sky-200">INTERNAL PERUSAHAAN</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <form action="{{ route('admin.settings.holidays.destroy', $holiday->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Hapus hari libur ini dari kalender?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data hari libur pada tahun ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('partials.pagination', ['paginator' => $holidays])
            @endif
        </div>
    </div>

    <!-- JavaScript Deteksi GPS Geolocation & Pratinjau Logo -->
    <script>
        function previewAppLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('brand-logo-preview-img');
                    const svg = document.getElementById('brand-logo-default-svg');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                    if (svg) {
                        svg.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function detectCurrentLocation() {
            const statusText = document.getElementById('gps-status-text');
            const latInput = document.getElementById('office_latitude');
            const lonInput = document.getElementById('office_longitude');
            const mapLink = document.getElementById('view-map-link');

            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi otomatis.');
                return;
            }

            if (statusText) statusText.textContent = 'Mendeteksi titik koordinat perangkat...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(8);
                    const lon = position.coords.longitude.toFixed(8);

                    if (latInput) latInput.value = lat;
                    if (lonInput) lonInput.value = lon;
                    if (mapLink) {
                        mapLink.href = `https://www.google.com/maps?q=${lat},${lon}`;
                    }
                    if (statusText) {
                        statusText.textContent = `✅ Koordinat terdeteksi (Akurasi: ±${Math.round(position.coords.accuracy)}m)`;
                    }
                },
                function(error) {
                    alert('Gagal mengambil koordinat: ' + error.message);
                    if (statusText) statusText.textContent = 'Gagal mendeteksi koordinat GPS.';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        document.addEventListener('DOMContentLoaded', function() {
            const latInput = document.getElementById('office_latitude');
            const lonInput = document.getElementById('office_longitude');
            const mapLink = document.getElementById('view-map-link');

            function syncMapLink() {
                if (latInput && lonInput && mapLink) {
                    const lat = latInput.value;
                    const lon = lonInput.value;
                    if (lat && lon) {
                        mapLink.href = `https://www.google.com/maps?q=${lat},${lon}`;
                    }
                }
            }

            if (latInput) latInput.addEventListener('input', syncMapLink);
            if (lonInput) lonInput.addEventListener('input', syncMapLink);
        });
    </script>
@endsection
