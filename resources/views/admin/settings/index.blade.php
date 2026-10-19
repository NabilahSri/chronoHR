@extends('layouts.app')

@section('title', 'Pengaturan Sistem — ChronoHR')
@section('page_title', 'Pengaturan Sistem')
@section('page_subtitle', 'Kelola titik acuan lokasi kantor, toleransi jam kerja, kebijakan WFH, dan jadwal penggajian.')

@section('content')
    <div class="w-full max-w-6xl mx-auto space-y-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: Titik Acuan Lokasi Kantor & Geofencing -->
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
                            <h2 class="text-sm font-bold text-slate-900">Titik Acuan Lokasi Kantor (Geofencing Presensi)</h2>
                            <p class="text-xs text-slate-500">Acuan radius kehadiran untuk Magang, WFH, dan karyawan on-site yang lupa RFID.</p>
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
                            <span>Buka di Maps</span>
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Latitude</label>
                            <input type="number" step="any" name="office_latitude" id="office_latitude"
                                value="{{ old('office_latitude', $officeLatitude) }}" required
                                class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Longitude</label>
                            <input type="number" step="any" name="office_longitude" id="office_longitude"
                                value="{{ old('office_longitude', $officeLongitude) }}" required
                                class="w-full px-3 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Radius Toleransi On-Site</label>
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
                        <h2 class="text-sm font-bold text-slate-900">Toleransi Waktu & Ambang Lembur</h2>
                        <p class="text-xs text-slate-500">Toleransi keterlambatan check-in pagi dan batas mulai hitung lembur setelah jam pulang.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Toleransi Keterlambatan Masuk</label>
                        <div class="relative w-full">
                            <input type="number" name="late_tolerance_minutes"
                                value="{{ old('late_tolerance_minutes', $lateTolerance) }}" min="0" max="120" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit (setelah 08:00)</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ambang Mulai Lembur Pulang</label>
                        <div class="relative w-full">
                            <input type="number" name="overtime_tolerance_minutes"
                                value="{{ old('overtime_tolerance_minutes', $overtimeTolerance) }}" min="0" max="180" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit (setelah 17:00)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Kebijakan Potongan WFH & Penggajian (2 Kolom Bersandingan) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card Potongan WFH -->
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
                                <h2 class="text-sm font-bold text-slate-900">Kebijakan Potongan WFH</h2>
                                <p class="text-xs text-slate-500">Persentase potongan gaji harian per hari WFH.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Karyawan Tetap (KARTAP)</label>
                                <div class="relative w-full">
                                    <input type="number" name="wfh_deduction_kartap_pct"
                                        value="{{ old('wfh_deduction_kartap_pct', $wfhDeductionKartap) }}" min="0" max="100" step="0.5" required
                                        class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Karyawan Kontrak (PKWT)</label>
                                <div class="relative w-full">
                                    <input type="number" name="wfh_deduction_pkwt_pct"
                                        value="{{ old('wfh_deduction_pkwt_pct', $wfhDeductionPkwt) }}" min="0" max="100" step="0.5" required
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Gajian (Publish Slip Gaji)</label>
                            <div class="relative w-full">
                                <input type="number" name="payday_date"
                                    value="{{ old('payday_date', $paydayDate) }}" min="1" max="31" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-xs font-mono font-bold focus:outline-none focus:bg-white focus:border-slate-900 transition-all">
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Setiap Bulan</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2">
                                Slip gaji dirilis otomatis dan dapat diakses personil per tanggal <strong>{{ $paydayDate }}</strong> setiap bulan.
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
    </div>

    <!-- JavaScript Deteksi GPS Geolocation -->
    <script>
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
