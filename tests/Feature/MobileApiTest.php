<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\Payroll;
use App\Models\ProjectIncentive;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_mobile_login_profile_password_and_logout_flow(): void
    {
        // 1. Login sebagai Karyawan PKWT
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'karyawan@test.com',
            'password' => 'password',
            'device_name' => 'Flutter-Android',
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure([
                'success',
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'employment_type',
                    'can_approve_leaves',
                    'roles',
                    'bank_account',
                    'ui_flags',
                ],
            ]);

        $token = $loginResponse->json('access_token');
        $this->assertNotEmpty($token);
        $this->assertEquals('PKWT', $loginResponse->json('user.employment_type'));
        $this->assertTrue($loginResponse->json('user.ui_flags.show_monthly_payslip_menu'));

        // 2. Get Profile dengan Bearer Token
        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/profile');

        $profileResponse->assertOk()
            ->assertJsonPath('data.email', 'karyawan@test.com');

        // 3. Update Password
        $pwdResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'password',
                'password' => 'newsecret123',
                'password_confirmation' => 'newsecret123',
            ]);

        $pwdResponse->assertOk()
            ->assertJsonPath('success', true);

        // 4. Logout menghapus token
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $logoutResponse->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_mobile_attendance_daily_report_and_checkout_gate_flow(): void
    {
        Storage::fake('public');

        $intern = User::where('employment_type', 'INTERN')->first();
        $today = now()->toDateString();

        Attendance::where('user_id', $intern->id)->whereDate('date', $today)->delete();
        DailyReport::where('user_id', $intern->id)->whereDate('date', $today)->delete();

        $token = $intern->createToken('flutter-test')->plainTextToken;

        // 1. Cek status absensi hari ini sebelum check-in
        $todayRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/attendance/today');

        $todayRes->assertOk()
            ->assertJsonPath('has_checked_in', false)
            ->assertJsonPath('can_checkout', false);

        // 2. Check-in WFH/WFA dengan unggah foto wajah + GPS
        $checkInRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'face_image' => UploadedFile::fake()->image('selfie.jpg'),
            ]);

        $checkInRes->assertStatus(201)
            ->assertJsonPath('success', true);

        // 3. Kirim rencana tugas pagi (Daily Report Morning)
        $morningRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/reports/morning', [
                'morning_planned_tasks' => "1. Integrasi halaman Home Flutter\n2. Uji endpoint presensi WFA",
            ]);

        $morningRes->assertStatus(201)
            ->assertJsonPath('data.status', 'MORNING_ONLY');

        // 4. Coba Checkout SEBELUM evaluasi sore selesai -> Harus ditolak (422)
        $earlyCheckoutRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/attendance/checkout');

        $earlyCheckoutRes->assertStatus(422)
            ->assertJsonPath('daily_report_status', 'MORNING_ONLY');

        // 5. Ambil rencana tugas pagi dari /api/reports/today
        $reportTodayRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/today');

        $reportTodayRes->assertOk()
            ->assertJsonPath('has_submitted_morning', true)
            ->assertJsonPath('is_completed', false);

        // 6. Kirim evaluasi tugas sore (Daily Report Evening -> COMPLETED)
        $eveningRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/reports/evening', [
                'evening_completed_tasks' => '1. Integrasi halaman Home Flutter (Selesai)',
                'evening_pending_tasks' => '2. Uji endpoint presensi WFA (Dilanjutkan besok)',
                'evening_additional_tasks' => 'Perbaikan tata letak kartu profil',
            ]);

        $eveningRes->assertOk()
            ->assertJsonPath('data.status', 'COMPLETED')
            ->assertJsonPath('can_checkout', true);

        // 7. Checkout setelah DailyReport COMPLETED -> Berhasil (200 OK) dengan lokasi & foto selfie
        $checkoutRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/attendance/checkout', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'face_image' => UploadedFile::fake()->image('selfie_out.jpg'),
            ]);

        $checkoutRes->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNotNull($checkoutRes->json('attendance.time_out'));
        $this->assertNotEmpty($checkoutRes->json('attendance.checkout_face_image_url'));

        // 8. Cek riwayat presensi & laporan tugas
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/attendance/history')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/reports/history')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_mobile_leave_submission_and_dynamic_approval_flow(): void
    {
        $employee = User::where('email', 'karyawan@test.com')->first();
        $approver = User::where('email', 'manager@test.com')->first();

        $empToken = $employee->createToken('emp-mobile')->plainTextToken;
        $mgrToken = $approver->createToken('mgr-mobile')->plainTextToken;

        // 1. Karyawan biasa tidak berhak mengakses /api/leaves/approvals (403)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/leaves/approvals')
            ->assertStatus(403);

        // 2. Karyawan mengajukan WFH/WFA lewat aplikasi mobile
        $submitLeaveRes = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/leaves', [
                'type' => 'WFH',
                'start_date' => '2026-11-24',
                'end_date' => '2026-11-25',
                'reason' => 'Koordinasi proyek dari rumah',
            ]);

        $submitLeaveRes->assertStatus(201)
            ->assertJsonPath('data.type', 'WFA')
            ->assertJsonPath('data.status', 'PENDING');

        $leaveId = $submitLeaveRes->json('data.id');

        // 3. Approver melihat daftar pengajuan PENDING dan menyetujuinya via HP
        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->getJson('/api/leaves/approvals')
            ->assertOk()
            ->assertJsonPath('success', true);

        $approveRes = $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson("/api/leaves/{$leaveId}/action", [
                'status' => 'APPROVED',
            ]);

        $approveRes->assertOk()
            ->assertJsonPath('data.status', 'APPROVED');

        // Pastikan tabel attendances terisi otomatis untuk tanggal tersebut
        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => '2026-11-24',
            'status' => 'WFA',
        ]);
    }

    public function test_mobile_payrolls_pdf_download_and_incentives_endpoints(): void
    {
        $employee = User::where('email', 'karyawan@test.com')->first();
        $intern = User::where('employment_type', 'INTERN')->first();

        $payroll = Payroll::create([
            'user_id' => $employee->id,
            'month' => 11,
            'year' => 2026,
            'total_working_days' => 22,
            'total_present_onsite' => 20,
            'total_wfa_days' => 2,
            'total_overtime_hours' => 3,
            'daily_salary_rate' => 204545.45,
            'wfa_deduction_amount' => 122727.27,
            'leave_deduction_amount' => 0,
            'overtime_pay' => 63000,
            'gross_salary' => 4563000,
            'net_salary' => 4440272.73,
            'status' => 'PUBLISHED',
        ]);

        ProjectIncentive::create([
            'user_id' => $intern->id,
            'project_name' => 'Aplikasi Mobile Flutter ChronoHR',
            'amount' => 1250000,
            'given_date' => '2026-11-15',
            'notes' => 'Selesai tepat waktu',
        ]);

        $empToken = $employee->createToken('emp-mobile')->plainTextToken;
        $internToken = $intern->createToken('intern-mobile')->plainTextToken;

        // 1. Daftar & Detail Slip Gaji Karyawan
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/payrolls')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson("/api/payrolls/{$payroll->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $payroll->id);

        // 2. Unduh PDF Slip Gaji
        $pdfRes = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->get("/api/payrolls/{$payroll->id}/download");

        $pdfRes->assertOk();
        $this->assertEquals('application/pdf', $pdfRes->headers->get('Content-Type'));

        // 3. Riwayat Insentif Proyek Magang
        $incRes = $this->withHeader('Authorization', 'Bearer ' . $internToken)
            ->getJson('/api/incentives');

        $incRes->assertOk()
            ->assertJsonPath('employment_type', 'INTERN')
            ->assertJsonPath('success', true);
    }

    public function test_hybrid_sanction_rules_auto_late_detection_payroll_deduction_and_mobile_api(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('email', 'karyawan@test.com')->first();

        // 1. Admin menambah aturan pelanggaran manual baru
        $this->actingAs($admin)->post(route('sanctions.rules.store'), [
            'code' => 'LATE-TEST-3X',
            'name' => 'Terlambat Masuk > 3 Kali Sebulan (Uji)',
            'category' => 'DISIPLIN_WAKTU',
            'trigger_type' => 'AUTO_LATE',
            'threshold_count' => 3,
            'default_level' => 'SP1',
            'default_deduction_amount' => 75000,
            'description' => 'Aturan uji keterlambatan > 3 kali',
            'is_active' => 1,
        ])->assertRedirect();

        // 2. Buat 4 catatan terlambat untuk karyawan pada bulan Desember 2026 (> 3 kali)
        for ($d = 1; $d <= 4; $d++) {
            Attendance::create([
                'user_id' => $employee->id,
                'date' => sprintf('2026-12-%02d', $d),
                'time_in' => '08:40:00',
                'time_out' => '17:05:00',
                'status' => 'HADIR_ON_SITE',
                'method' => 'RFID',
                'is_late' => true,
                'overtime_hours' => 0,
            ]);
        }

        // 3. Jalankan deteksi otomatis sanksi untuk periode 12/2026
        $this->actingAs($admin)->post(route('sanctions.detect'), [
            'month' => 12,
            'year' => 2026,
        ])->assertRedirect();

        $this->assertDatabaseHas('employee_sanctions', [
            'user_id' => $employee->id,
            'month' => 12,
            'year' => 2026,
            'source' => 'AUTO_DETECTED',
            'status' => 'ACTIVE',
        ]);

        // 4. Pastikan kalkulasi payroll Desember 2026 otomatis memperhitungkan potongan denda sanksi
        $payrollService = app(\App\Services\PayrollService::class);
        $payrollData = $payrollService->calculateForUser($employee, 12, 2026, 22);
        $this->assertGreaterThan(0, $payrollData['sanction_deduction_amount']);
        $this->assertGreaterThan(0, $payrollData['total_sanctions_count']);

        // 5. Pastikan halaman web /sanctions dapat dirender dengan normal (200 OK)
        $this->actingAs($admin)->get(route('sanctions.index', ['month' => 12, 'year' => 2026]))
            ->assertOk()
            ->assertSee('Manajemen Sanksi & Aturan Kedisiplinan');

        // 6. Karyawan mengecek sanksi miliknya lewat Mobile API (/api/sanctions)
        $empToken = $employee->createToken('emp-sanction-test')->plainTextToken;

        $sanctionsRes = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/sanctions');

        $sanctionsRes->assertOk()
            ->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(1, $sanctionsRes->json('summary.active_sanctions'));

        // 7. Uji unduh PDF Surat Peringatan (SP) di Web & Mobile API
        $sanctionRecord = \App\Models\EmployeeSanction::where('user_id', $employee->id)->first();
        $this->actingAs($admin)->get(route('sanctions.pdf', $sanctionRecord->id))
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->get("/api/sanctions/{$sanctionRecord->id}/download")
            ->assertOk();
    }

    public function test_rfid_tap_endpoint_holidays_calendar_attendance_override_and_api_docs(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('email', 'karyawan@test.com')->first();
        $employee->update(['rfid_uid' => 'RFID9988']);

        // 1. Uji Alat NodeMCU mode REGISTRATION + Auto-Detect UID di form admin
        $device = \App\Models\Device::create([
            'device_uid' => 'NODEMCU-TEST-01',
            'name' => 'Mesin RFID Lobi Uji',
            'location' => 'Lobi Utama',
            'mode' => 'REGISTRATION',
            'status' => 'ONLINE',
        ]);

        $this->postJson('/api/devices/rfid-tap', [
            'device_uid' => 'NODEMCU-TEST-01',
            'rfid_uid' => 'NEWCARD77',
        ])->assertOk()
          ->assertJsonPath('status', 'REGISTRATION_SCANNED')
          ->assertJsonPath('rfid_uid', 'NEWCARD77');

        $this->actingAs($admin)->getJson(route('admin.devices.latest-scan'))
            ->assertOk()
            ->assertJsonPath('rfid_uid', 'NEWCARD77');

        // 2. Uji Alat NodeMCU mode ATTENDANCE (Tap Masuk & Tap Pulang)
        $device->update(['mode' => 'ATTENDANCE']);

        $this->postJson('/api/devices/rfid-tap', [
            'device_uid' => 'NODEMCU-TEST-01',
            'rfid_uid' => 'RFID9988',
        ])->assertOk()
          ->assertJsonPath('action', 'check_in');

        // Tap pulang wajib gagal (422) jika laporan sore belum COMPLETED
        $this->postJson('/api/devices/rfid-tap', [
            'device_uid' => 'NODEMCU-TEST-01',
            'rfid_uid' => 'RFID9988',
        ])->assertStatus(422)
          ->assertJsonPath('action', 'check_out_blocked');

        // Isi laporan sore COMPLETED lalu tap pulang lagi
        \App\Models\DailyReport::create([
            'user_id' => $employee->id,
            'date' => now()->toDateString(),
            'morning_planned_tasks' => 'Tugas A',
            'evening_completed_tasks' => 'Tugas A',
            'status' => 'COMPLETED',
        ]);

        $this->postJson('/api/devices/rfid-tap', [
            'device_uid' => 'NODEMCU-TEST-01',
            'rfid_uid' => 'RFID9988',
        ])->assertOk()
          ->assertJsonPath('action', 'check_out');

        // 3. Uji Kalender Hari Libur Nasional & Perhitungan Hari Kerja Efektif
        $this->actingAs($admin)->post(route('admin.settings.holidays.store'), [
            'date' => '2026-11-18',
            'name' => 'Hari Libur Uji Sistem',
            'type' => 'NASIONAL',
        ])->assertRedirect();

        $effectiveDays = \App\Models\Holiday::calculateEffectiveWorkingDays(11, 2026);
        $this->assertEquals(20, $effectiveDays); // Nov 2026 has 21 weekdays minus 1 holiday on Wed Nov 18 = 20

        // 4. Uji Koreksi / Input Presensi Manual oleh Super Admin
        $this->actingAs($admin)->post(route('admin.attendances.override'), [
            'user_id' => $employee->id,
            'date' => '2026-11-10',
            'status' => 'HADIR_ON_SITE',
            'time_in' => '07:55',
            'time_out' => '18:00',
            'is_late' => 0,
            'overtime_hours' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => '2026-11-10',
            'overtime_hours' => 1,
        ]);

        // 5. Uji Halaman Dokumentasi API Mobile (/api-docs)
        $this->actingAs($admin)->get(route('api.docs'))
            ->assertOk()
            ->assertSee('Dokumentasi REST API Mobile (Flutter) & IoT RFID');
    }

    public function test_mobile_api_edge_cases_and_security_guards(): void
    {
        Storage::fake('public');

        $employee = User::where('email', 'karyawan@test.com')->first();
        $approver = User::where('email', 'manager@test.com')->first();
        $intern = User::where('employment_type', 'INTERN')->first();
        $today = now()->toDateString();

        Attendance::where('user_id', $employee->id)->whereDate('date', $today)->delete();
        DailyReport::where('user_id', $employee->id)->whereDate('date', $today)->delete();

        $empToken = $employee->createToken('emp-edge')->plainTextToken;
        $mgrToken = $approver->createToken('mgr-edge')->plainTextToken;
        $internToken = $intern->createToken('intern-edge')->plainTextToken;

        // 1. Morning report ditolak (422) jika belum check-in hari ini
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/reports/morning', [
                'morning_planned_tasks' => 'Tugas sebelum absen',
            ])
            ->assertStatus(422);

        // 2. Check-in WFH tanpa face_image ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('face_image');

        // 3. Izin WFH disetujui untuk hari ini (membuat row attendance tanpa time_in),
        //    lalu karyawan melakukan Mobile Check-In -> WAJIB mencatat time_in (bukan time_out)
        $leaveRes = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->post('/api/leaves', [
                'type' => 'WFH',
                'start_date' => $today,
                'end_date' => $today,
                'reason' => 'WFH hari ini',
                'attachment' => UploadedFile::fake()->image('bukti.jpg'),
            ], ['Accept' => 'application/json']);

        $leaveRes->assertStatus(201);
        $this->assertNotEmpty($leaveRes->json('data.attachment_url'));
        $leaveId = $leaveRes->json('data.id');

        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson("/api/leaves/{$leaveId}/action", ['status' => 'APPROVED'])
            ->assertOk();

        // Double action pada pengajuan yang sudah APPROVED ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson("/api/leaves/{$leaveId}/action", [
                'status' => 'REJECTED',
                'rejection_reason' => 'Coba ubah ulang',
            ])
            ->assertStatus(422);

        // Riwayat approval berdasarkan filter status=APPROVED dapat diakses
        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->getJson('/api/leaves/approvals?status=APPROVED')
            ->assertOk()
            ->assertJsonPath('filter_status', 'APPROVED');

        // Self-approval ditolak (403)
        $mgrLeaveRes = $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson('/api/leaves', [
                'type' => 'IZIN',
                'start_date' => $today,
                'end_date' => $today,
                'reason' => 'Urusan pribadi',
            ]);
        $mgrLeaveId = $mgrLeaveRes->json('data.id');

        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson("/api/leaves/{$mgrLeaveId}/action", ['status' => 'APPROVED'])
            ->assertStatus(403);

        // Mobile Check-In pada hari WFA yang sudah disetujui -> harus isi time_in dan time_out tetap null
        $wfhCheckIn = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'face_image' => UploadedFile::fake()->image('selfie_wfh.jpg'),
            ]);

        $wfhCheckIn->assertStatus(201);
        $this->assertNotNull($wfhCheckIn->json('attendance.time_in'));
        $this->assertNull($wfhCheckIn->json('attendance.time_out'));
        $this->assertNotEmpty($wfhCheckIn->json('attendance.face_image_url'));

        // 4. Daily Report tidak bisa ditimpa ulang lewat /reports/morning setelah COMPLETED
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/reports/morning', ['morning_planned_tasks' => 'Task 1'])
            ->assertStatus(201);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/reports/evening', ['evening_completed_tasks' => 'Task 1 selesai'])
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/reports/morning', ['morning_planned_tasks' => 'Coba timpa'])
            ->assertStatus(422);

        // 5. INTERN ditolak (403) mengakses slip gaji bulanan, karyawan ditolak mengakses slip DRAFT
        $this->withHeader('Authorization', 'Bearer ' . $internToken)
            ->getJson('/api/payrolls')
            ->assertStatus(403);

        $draftPayroll = Payroll::create([
            'user_id' => $employee->id,
            'month' => 10,
            'year' => 2026,
            'total_working_days' => 22,
            'total_present_onsite' => 22,
            'total_wfa_days' => 0,
            'total_overtime_hours' => 0,
            'daily_salary_rate' => 200000,
            'wfa_deduction_amount' => 0,
            'leave_deduction_amount' => 0,
            'overtime_pay' => 0,
            'gross_salary' => 4400000,
            'net_salary' => 4400000,
            'status' => 'DRAFT',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson("/api/payrolls/{$draftPayroll->id}")
            ->assertStatus(403);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->get("/api/payrolls/{$draftPayroll->id}/download")
            ->assertStatus(403);

        // 6. Detail slip gaji PUBLISHED menyertakan project_incentives pada earnings
        ProjectIncentive::create([
            'user_id' => $employee->id,
            'project_name' => 'Bonus Integrasi API',
            'amount' => 500000,
            'given_date' => '2026-09-15',
        ]);

        $publishedPayroll = Payroll::create([
            'user_id' => $employee->id,
            'month' => 9,
            'year' => 2026,
            'total_working_days' => 22,
            'total_present_onsite' => 22,
            'total_wfa_days' => 0,
            'total_overtime_hours' => 0,
            'daily_salary_rate' => 200000,
            'wfa_deduction_amount' => 0,
            'leave_deduction_amount' => 0,
            'overtime_pay' => 0,
            'gross_salary' => 4900000,
            'net_salary' => 4900000,
            'status' => 'PUBLISHED',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson("/api/payrolls/{$publishedPayroll->id}")
            ->assertOk()
            ->assertJsonPath('data.earnings.project_incentives', 500000);
    }

    public function test_only_regular_employee_and_intern_can_login_to_mobile_api(): void
    {
        // Role mobile yang diizinkan: karyawan (termasuk approver & multi-role finance+karyawan) dan intern
        foreach (['karyawan@test.com', 'manager@test.com', 'intern@test.com', 'finance@test.com'] as $allowedEmail) {
            $this->postJson('/api/login', [
                'email' => $allowedEmail,
                'password' => 'password',
            ])->assertOk()->assertJsonPath('success', true);
        }

        // Role khusus web tanpa role karyawan/intern ditolak masuk ke API Mobile (403)
        foreach (['admin@test.com', 'ceo@test.com'] as $webOnlyEmail) {
            $this->postJson('/api/login', [
                'email' => $webOnlyEmail,
                'password' => 'password',
            ])->assertStatus(403)->assertJsonPath('success', false);

            // Token lama milik role web juga wajib ditolak middleware auth.api (403)
            $webUser = User::where('email', $webOnlyEmail)->first();
            $token = $webUser->createToken('legacy-token')->plainTextToken;

            $this->withHeader('Authorization', 'Bearer ' . $token)
                ->getJson('/api/profile')
                ->assertStatus(403);
        }
    }

    public function test_employee_wfa_checkin_requires_approved_wfa_leave_while_intern_can_checkin(): void
    {
        Storage::fake('public');

        $employee = User::where('email', 'karyawan@test.com')->first();
        $approver = User::where('email', 'manager@test.com')->first();
        $intern = User::where('employment_type', 'INTERN')->first();
        $today = now()->toDateString();

        Attendance::whereIn('user_id', [$employee->id, $intern->id])->whereDate('date', $today)->delete();
        \App\Models\LeaveRequest::whereIn('user_id', [$employee->id, $intern->id])->delete();

        $empToken = $employee->createToken('emp-wfa-gate')->plainTextToken;
        $mgrToken = $approver->createToken('mgr-wfa-gate')->plainTextToken;
        $internToken = $intern->createToken('intern-wfa-gate')->plainTextToken;

        // 1. Karyawan belum mengajukan WFA -> can_mobile_checkin = false & POST /attendance/wfh ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('has_approved_wfa_today', false)
            ->assertJsonPath('can_mobile_checkin', false);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'face_image' => UploadedFile::fake()->image('selfie1.jpg'),
            ])
            ->assertStatus(422);

        // 2. Karyawan mengajukan WFH namun masih PENDING -> tetap ditolak (422)
        $leaveRes = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/leaves', [
                'type' => 'WFH',
                'start_date' => $today,
                'end_date' => $today,
                'reason' => 'WFH hari ini',
            ])->assertStatus(201);

        $leaveId = $leaveRes->json('data.id');

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('wfa_request_today_status', 'PENDING')
            ->assertJsonPath('can_mobile_checkin', false);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'face_image' => UploadedFile::fake()->image('selfie2.jpg'),
            ])
            ->assertStatus(422)
            ->assertJsonPath('wfa_request_today_status', 'PENDING');

        // 3. Setelah di-ACC (APPROVED) -> can_mobile_checkin = true & check-in WFH berhasil (201)
        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson("/api/leaves/{$leaveId}/action", ['status' => 'APPROVED'])
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('has_approved_wfa_today', true)
            ->assertJsonPath('wfa_request_today_status', 'APPROVED')
            ->assertJsonPath('can_mobile_checkin', true);

        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'face_image' => UploadedFile::fake()->image('selfie3.jpg'),
            ])
            ->assertStatus(201)
            ->assertJsonPath('attendance.status', 'WFA');

        // 4. Magang (INTERN) tetap dapat check-in harian lewat mobile tanpa pengajuan WFA (tercatat HADIR_ON_SITE)
        $this->withHeader('Authorization', 'Bearer ' . $internToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('can_mobile_checkin', true);

        $this->withHeader('Authorization', 'Bearer ' . $internToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'face_image' => UploadedFile::fake()->image('selfie_intern.jpg'),
            ])
            ->assertStatus(201)
            ->assertJsonPath('attendance.status', 'HADIR_ON_SITE');
    }

    public function test_employee_forgot_rfid_onsite_checkin_enforces_strict_office_geofence(): void
    {
        Storage::fake('public');

        \App\Models\Setting::updateOrCreate(['key' => 'office_latitude'], ['value' => '-6.20880000']);
        \App\Models\Setting::updateOrCreate(['key' => 'office_longitude'], ['value' => '106.84560000']);
        \App\Models\Setting::updateOrCreate(['key' => 'office_radius_meters'], ['value' => '100']);

        $employee = User::where('email', 'karyawan@test.com')->first();
        $today = now()->toDateString();

        Attendance::where('user_id', $employee->id)->whereDate('date', $today)->delete();
        \App\Models\LeaveRequest::where('user_id', $employee->id)->delete();

        $empToken = $employee->createToken('emp-forgot-rfid')->plainTextToken;

        // 1. /attendance/today melaporkan can_onsite_checkin = true & can_mobile_checkin = false
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('can_mobile_checkin', false)
            ->assertJsonPath('can_onsite_checkin', true);

        // 2. Mode ON_SITE (Lupa RFID) di LUAR radius kantor -> WAJIB ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'presence_type' => 'ON_SITE',
                'face_image' => UploadedFile::fake()->image('selfie_outside.jpg'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('location');

        // 3. Mode ON_SITE (Lupa RFID) di DALAM radius kantor -> BERHASIL (201) dengan status HADIR_ON_SITE
        $res = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/wfh', [
                'latitude' => -6.20885000,
                'longitude' => 106.84565000,
                'presence_type' => 'ON_SITE',
                'face_image' => UploadedFile::fake()->image('selfie_office.jpg'),
            ]);

        $res->assertStatus(201)
            ->assertJsonPath('is_within_radius', true)
            ->assertJsonPath('attendance.status', 'HADIR_ON_SITE')
            ->assertJsonPath('attendance.method', 'FACIAL_LOCATION');
        $this->assertNotEmpty($res->json('attendance.face_image_url'));
    }

    public function test_checkout_enforces_photo_gps_and_onsite_geofence_while_wfa_is_radius_free(): void
    {
        Storage::fake('public');

        \App\Models\Setting::updateOrCreate(['key' => 'office_latitude'], ['value' => '-6.20880000']);
        \App\Models\Setting::updateOrCreate(['key' => 'office_longitude'], ['value' => '106.84560000']);
        \App\Models\Setting::updateOrCreate(['key' => 'office_radius_meters'], ['value' => '100']);

        $employee = User::where('email', 'karyawan@test.com')->first();
        $approver = User::where('email', 'manager@test.com')->first();
        $today = now()->toDateString();

        Attendance::whereIn('user_id', [$employee->id, $approver->id])->whereDate('date', $today)->delete();
        DailyReport::whereIn('user_id', [$employee->id, $approver->id])->whereDate('date', $today)->delete();

        $empToken = $employee->createToken('emp-checkout-onsite')->plainTextToken;
        $mgrToken = $approver->createToken('mgr-checkout-wfa')->plainTextToken;

        // A. Karyawan On-Site (hadir di kantor) + Daily Report COMPLETED
        Attendance::create([
            'user_id' => $employee->id,
            'date' => $today,
            'time_in' => '08:00:00',
            'status' => 'HADIR_ON_SITE',
            'method' => 'RFID',
            'is_late' => false,
            'overtime_hours' => 0,
        ]);
        DailyReport::create([
            'user_id' => $employee->id,
            'date' => $today,
            'morning_planned_tasks' => '1. Kerja kantor',
            'evening_completed_tasks' => '1. Kerja kantor',
            'status' => 'COMPLETED',
        ]);

        // /attendance/today mengembalikan checkout_mode = RFID_OR_ONSITE_GEOFENCE
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('can_checkout', true)
            ->assertJsonPath('checkout_mode', 'RFID_OR_ONSITE_GEOFENCE');

        // Checkout tanpa foto/GPS ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/checkout')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude', 'face_image']);

        // Checkout On-Site (lupa RFID) di LUAR radius kantor ditolak (422)
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/checkout', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'face_image' => UploadedFile::fake()->image('out_outside.jpg'),
            ])
            ->assertStatus(422)
            ->assertJsonPath('is_within_radius', false);

        // Checkout On-Site (lupa RFID) di DALAM radius kantor + foto selfie -> BERHASIL (200)
        $onsiteOut = $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->postJson('/api/attendance/checkout', [
                'latitude' => -6.20885000,
                'longitude' => 106.84565000,
                'face_image' => UploadedFile::fake()->image('out_office.jpg'),
            ]);

        $onsiteOut->assertOk()
            ->assertJsonPath('is_within_radius', true);
        $this->assertNotNull($onsiteOut->json('attendance.time_out'));
        $this->assertNotEmpty($onsiteOut->json('attendance.checkout_face_image_url'));

        // B. Karyawan WFA + Daily Report COMPLETED -> bebas radius tapi tetap wajib foto & GPS
        Attendance::create([
            'user_id' => $approver->id,
            'date' => $today,
            'time_in' => '08:00:00',
            'status' => 'WFA',
            'method' => 'FACIAL_LOCATION',
            'is_late' => false,
            'overtime_hours' => 0,
        ]);
        DailyReport::create([
            'user_id' => $approver->id,
            'date' => $today,
            'morning_planned_tasks' => '1. Kerja WFA',
            'evening_completed_tasks' => '1. Kerja WFA',
            'status' => 'COMPLETED',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('checkout_mode', 'MOBILE_FREE');

        $wfaOut = $this->withHeader('Authorization', 'Bearer ' . $mgrToken)
            ->postJson('/api/attendance/checkout', [
                'latitude' => -6.91750000,
                'longitude' => 107.61910000,
                'face_image' => UploadedFile::fake()->image('out_wfa.jpg'),
            ]);

        $wfaOut->assertOk();
        $this->assertNotNull($wfaOut->json('attendance.time_out'));
        $this->assertNotEmpty($wfaOut->json('attendance.checkout_face_image_url'));
    }

    public function test_dynamic_work_start_and_end_time_settings_drive_late_and_overtime_calculation(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('email', 'karyawan@test.com')->first();
        $empToken = $employee->createToken('emp-schedule')->plainTextToken;

        // 1. Admin mengatur jam masuk 09:00, jam pulang 18:00, toleransi terlambat 15m, ambang lembur 30m
        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'work_start_time' => '09:00',
            'work_end_time' => '18:00',
            'late_tolerance_minutes' => 15,
            'overtime_tolerance_minutes' => 30,
            'wfa_deduction_kartap_pct' => 10,
            'wfa_deduction_pkwt_pct' => 30,
            'payday_date' => 25,
        ])->assertRedirect();

        // 2. Endpoint /api/attendance/today mengembalikan jadwal kerja dinamis
        $this->withHeader('Authorization', 'Bearer ' . $empToken)
            ->getJson('/api/attendance/today')
            ->assertOk()
            ->assertJsonPath('work_schedule.work_start_time', '09:00')
            ->assertJsonPath('work_schedule.work_end_time', '18:00')
            ->assertJsonPath('work_schedule.late_tolerance_minutes', 15)
            ->assertJsonPath('work_schedule.overtime_tolerance_minutes', 30);

        // 3. Verifikasi kalkulasi terlambat & lembur mengikuti jam dinamis (09:00 - 18:00)
        $service = app(\App\Services\AttendanceService::class);
        $this->assertFalse($service->isLateAt(\Carbon\Carbon::parse('2026-11-24 09:10:00')));
        $this->assertTrue($service->isLateAt(\Carbon\Carbon::parse('2026-11-24 09:16:00')));
        $this->assertEquals(0, $service->calculateOvertimeHours(\Carbon\Carbon::parse('2026-11-24 18:20:00')));
        $this->assertEquals(1, $service->calculateOvertimeHours(\Carbon\Carbon::parse('2026-11-24 19:05:00')));
        $this->assertEquals(2, $service->calculateOvertimeHours(\Carbon\Carbon::parse('2026-11-24 20:15:00')));
    }

    public function test_public_branding_endpoint_reflects_web_settings_changes(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'app_name' => 'NusantaraHR',
            'app_slogan' => 'Presensi Modern & Cepat',
            'late_tolerance_minutes' => 15,
            'overtime_tolerance_minutes' => 30,
            'wfa_deduction_kartap_pct' => 10,
            'wfa_deduction_pkwt_pct' => 30,
            'payday_date' => 25,
        ])->assertRedirect();

        $this->getJson('/api/branding')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.app_name', 'NusantaraHR')
            ->assertJsonPath('data.app_slogan', 'Presensi Modern & Cepat');
    }
}
