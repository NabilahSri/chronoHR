@extends('layouts.app')

@section('title', 'Dokumentasi REST API Mobile & IoT - ChronoHR')
@section('page_title', 'Dokumentasi REST API Mobile (Flutter) & IoT RFID')
@section('page_subtitle', 'Referensi spesifikasi endpoint, header autentikasi Bearer Token, serta format JSON untuk integrasi aplikasi Flutter Karyawan/Magang dan perangkat NodeMCU RFID.')

@section('content')
    <div class="space-y-6">
        <!-- Ringkasan & Tombol Unduh Postman -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-slate-900 text-white text-[11px] font-mono font-bold">
                    <span>BASE URL: {{ url('/api') }}</span>
                </div>
                <h2 class="text-base font-bold text-slate-900">Panduan Integrasi Mobile Flutter & Perangkat IoT</h2>
                <p class="text-xs text-slate-600 max-w-3xl">
                    Seluruh endpoint mobile (kecuali <code class="font-mono font-bold text-slate-800">/api/login</code> dan <code class="font-mono font-bold text-slate-800">/api/devices/rfid-tap</code>) wajib menyertakan header
                    <code class="font-mono font-bold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200">Authorization: Bearer &lt;access_token&gt;</code>
                    dan <code class="font-mono font-bold text-slate-800 bg-slate-100 px-1.5 py-0.5 rounded">Accept: application/json</code>.
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ asset('chronoHR_mobile_api.postman_collection.json') }}" download="chronoHR_mobile_api.postman_collection.json"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-xs transition">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Koleksi Postman (.JSON)</span>
                </a>
            </div>
        </div>

        @php
            $apiGroups = [
                [
                    'title' => '1. Autentikasi & Profil (Auth & Profile)',
                    'desc' => 'Digunakan untuk masuk ke aplikasi Flutter, mengecek multi-role, melihat profil & rekening bank, serta mengganti kata sandi.',
                    'endpoints' => [
                        ['method' => 'POST', 'uri' => '/api/login', 'auth' => false, 'desc' => 'Login menggunakan email & password. Mengembalikan access_token, profil user, status (KARTAP/PKWT/INTERN), list roles, dan flag can_approve_leaves.', 'params' => 'email, password, device_name (opsional)'],
                        ['method' => 'POST', 'uri' => '/api/logout', 'auth' => true, 'desc' => 'Menghapus token sesi aktif saat pengguna keluar dari aplikasi mobile.', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/profile', 'auth' => true, 'desc' => 'Mengambil detail profil lengkap, nomor UID RFID, serta informasi rekening bank yang terdaftar.', 'params' => '-'],
                        ['method' => 'PUT', 'uri' => '/api/profile/password', 'auth' => true, 'desc' => 'Mengganti kata sandi akun dari aplikasi mobile.', 'params' => 'current_password, password, password_confirmation'],
                    ],
                ],
                [
                    'title' => '2. Presensi & Riwayat Kehadiran (Attendances)',
                    'desc' => 'Mendukung presensi mode WFA / Magang (Foto Wajah + GPS) serta Checkout sore yang dikunci oleh status penyelesaian Daily Report.',
                    'endpoints' => [
                        ['method' => 'GET', 'uri' => '/api/attendance/today', 'auth' => true, 'desc' => 'Mengecek status presensi hari ini, jam masuk/pulang, apakah Daily Report pagi & sore sudah diisi, serta flag can_checkout untuk tombol Home Screen Flutter.', 'params' => '-'],
                        ['method' => 'POST', 'uri' => '/api/attendance/wfa', 'auth' => true, 'desc' => 'Check-in via mobile (juga tersedia alias /api/attendance/wfh). Mendukung multipart/form-data foto selfie wajah & koordinat GPS.', 'params' => 'latitude, longitude, face_image (file opsional), presence_type (WFA/ON_SITE/AUTO)'],
                        ['method' => 'POST', 'uri' => '/api/attendance/checkout', 'auth' => true, 'desc' => 'Checkout sore via HP. Memvalidasi apakah DailyReport sore sudah COMPLETED (mengembalikan 422 jika belum), lalu otomatis menghitung jam lembur.', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/attendance/history', 'auth' => true, 'desc' => 'Menampilkan riwayat presensi bulanan lengkap dengan ringkasan hadir kantor, WFA, terlambat, dan jam lembur.', 'params' => '?month=11&year=2026'],
                    ],
                ],
                [
                    'title' => '3. Laporan Tugas Harian Pagi & Sore (Daily Reports)',
                    'desc' => 'Alur akuntabilitas kerja harian yang membuka akses checkout sore karyawan dan magang.',
                    'endpoints' => [
                        ['method' => 'GET', 'uri' => '/api/reports/today', 'auth' => true, 'desc' => 'Mengambil rencana tugas pagi hari ini agar dapat ditampilkan kembali saat karyawan mengisi evaluasi sore.', 'params' => '-'],
                        ['method' => 'POST', 'uri' => '/api/reports/morning', 'auth' => true, 'desc' => 'Mengirim daftar rencana tugas pagi (morning_planned_tasks) dan mengubah status menjadi MORNING_ONLY.', 'params' => 'morning_planned_tasks (array string)'],
                        ['method' => 'POST', 'uri' => '/api/reports/evening', 'auth' => true, 'desc' => 'Mengirim evaluasi sore (completed, pending, additional tasks) dan mengubah status menjadi COMPLETED sehingga checkout terbuka.', 'params' => 'evening_completed_tasks (array), evening_pending_tasks (array), evening_additional_tasks (array)'],
                        ['method' => 'GET', 'uri' => '/api/reports/history', 'auth' => true, 'desc' => 'Melihat arsip laporan tugas harian pada bulan berjalan.', 'params' => '?month=11&year=2026'],
                    ],
                ],
                [
                    'title' => '4. Manajemen Perizinan & Persetujuan Dinamis (Leave Requests)',
                    'desc' => 'Pengajuan Sakit, Izin, atau WFA sekaligus menu persetujuan bagi karyawan dengan hak can_approve_leaves = true.',
                    'endpoints' => [
                        ['method' => 'GET', 'uri' => '/api/leaves', 'auth' => true, 'desc' => 'Mengambil daftar riwayat pengajuan izin/sakit/WFA milik pengguna sendiri.', 'params' => '?status=PENDING|APPROVED|REJECTED'],
                        ['method' => 'POST', 'uri' => '/api/leaves', 'auth' => true, 'desc' => 'Mengajukan izin/sakit/WFA baru dengan dukungan unggah lampiran surat dokter/bukti pendukung.', 'params' => 'type (SAKIT/IZIN/WFA), start_date, end_date, reason, attachment (file opsional)'],
                        ['method' => 'GET', 'uri' => '/api/leaves/approvals', 'auth' => true, 'desc' => 'Khusus Approver (can_approve_leaves = true): mengambil daftar pengajuan karyawan lain yang menunggu persetujuan.', 'params' => '?status=PENDING'],
                        ['method' => 'POST', 'uri' => '/api/leaves/{id}/action', 'auth' => true, 'desc' => 'Khusus Approver: menyetujui (APPROVED) atau menolak (REJECTED) pengajuan izin. Jika disetujui, tabel attendances otomatis terisi.', 'params' => 'status (APPROVED/REJECTED), rejection_reason'],
                    ],
                ],
                [
                    'title' => '5. Finansial: Slip Gaji & Insentif Proyek (Payrolls & Incentives)',
                    'desc' => 'Transparansi kompensasi bulanan (hanya menampilkan slip berstatus PUBLISHED) dan bonus proyek.',
                    'endpoints' => [
                        ['method' => 'GET', 'uri' => '/api/payrolls', 'auth' => true, 'desc' => 'Mengambil daftar riwayat slip gaji bulanan milik pengguna yang sudah berstatus PUBLISHED.', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/payrolls/{id}', 'auth' => true, 'desc' => 'Melihat rincian lengkap perhitungan 1 slip gaji (gaji pokok, potongan WFA, potongan sanksi, lembur, insentif, & gaji bersih).', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/payrolls/{id}/download', 'auth' => true, 'desc' => 'Mengunduh dokumen PDF Slip Gaji Resmi berformat A4.', 'params' => '?preview=1 (opsional)'],
                        ['method' => 'GET', 'uri' => '/api/incentives', 'auth' => true, 'desc' => 'Melihat riwayat perolehan bonus/insentif proyek (khusus Magang/Intern maupun karyawan reguler).', 'params' => '-'],
                    ],
                ],
                [
                    'title' => '6. Sanksi & Evaluasi Kedisiplinan (Sanctions & SP PDF)',
                    'desc' => 'Transparansi catatan pelanggaran, tingkat Surat Peringatan (SP1-SP3), dan unduh dokumen SP resmi.',
                    'endpoints' => [
                        ['method' => 'GET', 'uri' => '/api/sanctions', 'auth' => true, 'desc' => 'Melihat riwayat teguran dan Surat Peringatan (SP1/SP2/SP3) milik pengguna beserta ringkasan status aktif.', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/sanctions/{id}', 'auth' => true, 'desc' => 'Melihat detail pelanggaran, kronologi alasan sanksi, dan nominal potongan gaji.', 'params' => '-'],
                        ['method' => 'GET', 'uri' => '/api/sanctions/{id}/download', 'auth' => true, 'desc' => 'Mengunduh dokumen PDF Surat Peringatan (SP) resmi dari HRD.', 'params' => '?preview=1 (opsional)'],
                    ],
                ],
                [
                    'title' => '7. Integrasi Perangkat IoT NodeMCU RFID (Hardware)',
                    'desc' => 'Endpoint komunikasi mesin fisik NodeMCU ESP8266 untuk pendaftaran kartu baru dan tap presensi masuk/pulang.',
                    'endpoints' => [
                        ['method' => 'POST', 'uri' => '/api/devices/rfid-tap', 'auth' => false, 'desc' => 'Menerima tap kartu RFID dari alat fisik. Jika mode alat REGISTRATION, menyimpan UID untuk form karyawan; jika ATTENDANCE, memproses check-in pagi atau check-out sore.', 'params' => 'device_code, rfid_uid, firmware_version (opsional)'],
                        ['method' => 'POST', 'uri' => '/devices/heartbeat', 'auth' => false, 'desc' => 'Sinkronisasi status online (ping) berkala dari perangkat NodeMCU dan mengambil mode operasi alat saat ini.', 'params' => 'device_code, ip_address, firmware_version'],
                    ],
                ],
            ];
        @endphp

        @foreach ($apiGroups as $group)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">{{ $group['title'] }}</h3>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $group['desc'] }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-600 border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3 w-24">Metode</th>
                                <th class="px-4 py-3 w-64">Endpoint URI</th>
                                <th class="px-4 py-3 w-28">Autentikasi</th>
                                <th class="px-4 py-3">Fungsi & Keterangan</th>
                                <th class="px-5 py-3 w-64">Parameter / Payload</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($group['endpoints'] as $ep)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @if ($ep['method'] === 'GET')
                                            <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">GET</span>
                                        @elseif ($ep['method'] === 'POST')
                                            <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-sky-50 text-sky-700 border border-sky-200">POST</span>
                                        @elseif ($ep['method'] === 'PUT')
                                            <span class="px-2.5 py-1 rounded-md text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">PUT</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-mono font-bold text-slate-900 whitespace-nowrap">
                                        {{ $ep['uri'] }}
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @if ($ep['auth'])
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300">Bearer Token</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Publik / IoT</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-700 leading-relaxed">
                                        {{ $ep['desc'] }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-[11px] text-slate-600">
                                        {{ $ep['params'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endsection
