<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DailyReportSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $users = User::pluck('id', 'email');

        // 1. Andi Staff (Hari ini: Lengkap Pagi + Sore)
        if (isset($users['karyawan@test.com'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['karyawan@test.com'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:02:00',
                    'morning_planned_tasks' => "1. Mengembangkan integrasi webhook IoT ke server\n2. Melakukan unit testing endpoint presensi RFID\n3. Daily standup koordinasi sprint tim",
                    'evening_submitted_at' => '16:45:00',
                    'evening_completed_tasks' => "1. Mengembangkan integrasi webhook IoT ke server (Selesai 100%)\n3. Daily standup koordinasi sprint tim (Selesai)",
                    'evening_pending_tasks' => "2. Melakukan unit testing endpoint presensi RFID (Tersisa 5 test case, dilanjutkan besok)",
                    'evening_additional_tasks' => "1. Fix error validasi CSRF token pada route webhook device heartbeat",
                    'status' => 'COMPLETED',
                ]
            );

            // Kemarin juga lengkap
            DailyReport::updateOrCreate(
                ['user_id' => $users['karyawan@test.com'], 'date' => $yesterday],
                [
                    'morning_submitted_at' => '08:05:00',
                    'morning_planned_tasks' => "1. Setup migrasi tabel daily reports\n2. Desain arsitektur database slip gaji",
                    'evening_submitted_at' => '17:00:00',
                    'evening_completed_tasks' => "1. Setup migrasi tabel daily reports (Selesai)\n2. Desain arsitektur database slip gaji (Selesai)",
                    'evening_pending_tasks' => null,
                    'evening_additional_tasks' => "1. Koordinasi teknis skema relational user",
                    'status' => 'COMPLETED',
                ]
            );
        }

        // 2. Dewi Anastasia (Hari ini: Lengkap Pagi + Sore)
        if (isset($users['dewi@chrono.id'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['dewi@chrono.id'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:12:00',
                    'morning_planned_tasks' => "1. Pengecekan sensor RFID RC522 pada device Gate 1 & Gate 2\n2. Dokumentasi skema pinout NodeMCU ESP8266\n3. Verifikasi kartu fisik karyawan baru",
                    'evening_submitted_at' => '16:55:00',
                    'evening_completed_tasks' => "1. Pengecekan sensor RFID RC522 pada device Gate 1 & Gate 2 (Normal)\n2. Dokumentasi skema pinout NodeMCU ESP8266 (Selesai)",
                    'evening_pending_tasks' => "3. Verifikasi kartu fisik karyawan baru (Kartu fisik belum tiba dari vendor)",
                    'evening_additional_tasks' => "1. Penggantian kabel jumper koneksi SPI yang kendor di Gate 1",
                    'status' => 'COMPLETED',
                ]
            );
        }

        // 3. Dimas Setiawan (Hari ini: Lengkap Pagi + Sore)
        if (isset($users['dimas@chrono.id'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['dimas@chrono.id'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:15:00',
                    'morning_planned_tasks' => "1. Konfigurasi access point Wi-Fi IoT di ruang server\n2. Uji coba latensi ping device NodeMCU setiap 60 detik",
                    'evening_submitted_at' => '17:05:00',
                    'evening_completed_tasks' => "1. Konfigurasi access point Wi-Fi IoT di ruang server (Selesai)\n2. Uji coba latensi ping device NodeMCU (Rata-rata 18ms, stabil)",
                    'evening_pending_tasks' => null,
                    'evening_additional_tasks' => "1. Backup file firmware NodeMCU ke Google Drive engineering",
                    'status' => 'COMPLETED',
                ]
            );
        }

        // 4. Budi Supervisor (Hari ini: Lengkap Pagi + Sore)
        if (isset($users['manager@test.com'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['manager@test.com'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:01:00',
                    'morning_planned_tasks' => "1. Review dan verifikasi permohonan cuti tahunan & WFH divisi\n2. Evaluasi rekapitulasi keterlambatan presensi mingguan\n3. Meeting 1-on-1 dengan personil magang",
                    'evening_submitted_at' => '16:50:00',
                    'evening_completed_tasks' => "1. Review dan verifikasi permohonan cuti tahunan & WFH divisi (Semua permohonan selesai diproses)\n3. Meeting 1-on-1 dengan personil magang (Selesai)",
                    'evening_pending_tasks' => "2. Evaluasi keterlambatan mingguan (Menunggu rekapitulasi audit HRD)",
                    'evening_additional_tasks' => "1. Sosialisasi SOP baru presensi RFID kepada divisi operasional",
                    'status' => 'COMPLETED',
                ]
            );
        }

        // 5. Fajar Nugraha (Hari ini: Baru Pagi / MORNING_ONLY)
        if (isset($users['fajar@chrono.id'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['fajar@chrono.id'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:25:00',
                    'morning_planned_tasks' => "1. Optimasi query rekapitulasi slip gaji & export laporan excel\n2. Review code pull request modul persetujuan cuti\n3. Benchmarking response time server lokal",
                    'evening_submitted_at' => null,
                    'evening_completed_tasks' => null,
                    'evening_pending_tasks' => null,
                    'evening_additional_tasks' => null,
                    'status' => 'MORNING_ONLY',
                ]
            );
        }

        // 6. Rian Intern (Hari ini: Baru Pagi / MORNING_ONLY)
        if (isset($users['intern@test.com'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['intern@test.com'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:05:00',
                    'morning_planned_tasks' => "1. Mempelajari dokumentasi framework Laravel 12 & Tailwind CSS\n2. Merapikan form-form agar full-width sesuai standar ChronoHR\n3. Membantu QA testing alur pengajuan WFH",
                    'evening_submitted_at' => null,
                    'evening_completed_tasks' => null,
                    'evening_pending_tasks' => null,
                    'evening_additional_tasks' => null,
                    'status' => 'MORNING_ONLY',
                ]
            );
        }

        // 7. Siti Finance (Hari ini: Baru Pagi / MORNING_ONLY)
        if (isset($users['finance@test.com'])) {
            DailyReport::updateOrCreate(
                ['user_id' => $users['finance@test.com'], 'date' => $today],
                [
                    'morning_submitted_at' => '08:30:00',
                    'morning_planned_tasks' => "1. Verifikasi kalkulasi potongan gaji WFH KARTAP & PKWT\n2. Rekonsiliasi pembayaran insentif proyek magang\n3. Persiapan rilis draft slip gaji bulanan",
                    'evening_submitted_at' => null,
                    'evening_completed_tasks' => null,
                    'evening_pending_tasks' => null,
                    'evening_additional_tasks' => null,
                    'status' => 'MORNING_ONLY',
                ]
            );
        }

        // 8. Maya Kartika -> Belum lapor sama sekali hari ini (NOT_SUBMITTED)
    }
}
