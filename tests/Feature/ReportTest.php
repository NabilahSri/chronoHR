<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    public function test_super_admin_can_access_attendance_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.attendances'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Presensi');
        $response->assertSee('Menu Laporan');
        $response->assertSee('Slip Gaji');
        $response->assertSee('Pengajuan');
    }

    public function test_super_admin_can_access_payroll_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.payrolls'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Slip Gaji');
        $response->assertSee('Rincian Slip Penggajian Karyawan');
    }

    public function test_super_admin_can_access_leave_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.leaves'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Pengajuan');
        $response->assertSee('Komposisi Pengajuan');
    }

    public function test_finance_role_can_access_reports(): void
    {
        $finance = User::where('email', 'finance@test.com')->first();

        $response = $this->actingAs($finance)->get(route('reports.attendances'));
        $response->assertOk();

        $response = $this->actingAs($finance)->get(route('reports.payrolls'));
        $response->assertOk();

        $response = $this->actingAs($finance)->get(route('reports.leaves'));
        $response->assertOk();
    }

    public function test_regular_karyawan_cannot_access_reports(): void
    {
        $karyawan = User::where('email', 'karyawan@test.com')->first();

        $response = $this->actingAs($karyawan)->get(route('reports.attendances'));
        $response->assertForbidden();

        $response = $this->actingAs($karyawan)->get(route('reports.payrolls'));
        $response->assertForbidden();

        $response = $this->actingAs($karyawan)->get(route('reports.leaves'));
        $response->assertForbidden();
    }

    public function test_super_admin_can_export_attendances(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.attendances.export'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_super_admin_can_export_payrolls(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.payrolls.export'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_super_admin_can_export_leaves(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.leaves.export'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_payroll_report_has_no_status_slip_filter(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.payrolls'));

        $response->assertOk();
        $response->assertDontSee('name="status"', false);
        $response->assertSee('Export ke Excel');
    }

    public function test_admin_can_update_and_view_payday_setting(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $viewResponse = $this->actingAs($admin)->get(route('admin.settings.index'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Tanggal Gajian');
        $viewResponse->assertSee('payday_date');

        $updateResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'late_tolerance_minutes' => 15,
            'overtime_tolerance_minutes' => 30,
            'wfh_deduction_kartap_pct' => 10,
            'wfh_deduction_pkwt_pct' => 30,
            'payday_date' => 28,
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('28', \App\Models\Setting::getValue('payday_date'));
    }

    public function test_super_admin_can_access_daily_tasks_monitoring(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.daily-tasks'));

        $response->assertOk();
        $response->assertSee('Monitoring Laporan Task Harian');
        $response->assertSee('Rencana Pagi Masuk');
        $response->assertSee('Export ke Excel');
    }

    public function test_super_admin_can_export_daily_tasks(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.daily-tasks.export'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    public function test_can_store_and_update_daily_task_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $user = User::where('email', 'karyawan@test.com')->first();
        $testDate = '2026-10-25';

        // 1. Simpan laporan pagi
        $storeResponse = $this->actingAs($admin)->post(route('reports.daily-tasks.store'), [
            'user_id' => $user->id,
            'date' => $testDate,
            'morning_planned_tasks' => "1. Task testing pagi A\n2. Task testing pagi B",
        ]);

        $storeResponse->assertRedirect();

        $report = \App\Models\DailyReport::where('user_id', $user->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($report);
        $this->assertEquals('MORNING_ONLY', $report->status);
        $this->assertStringContainsString('Task testing pagi A', $report->morning_planned_tasks);

        // 2. Update evaluasi sore
        $updateResponse = $this->actingAs($admin)->put(route('reports.daily-tasks.update', $report), [
            'evening_completed_tasks' => '1. Task testing pagi A (Selesai)',
            'evening_pending_tasks' => '2. Task testing pagi B (Pending)',
            'evening_additional_tasks' => '1. Task dadakan C',
        ]);

        $updateResponse->assertRedirect();

        $report->refresh();
        $this->assertEquals('COMPLETED', $report->status);
        $this->assertStringContainsString('Task testing pagi A (Selesai)', $report->evening_completed_tasks);
        $this->assertStringContainsString('Task dadakan C', $report->evening_additional_tasks);
    }

    public function test_can_update_daily_task_using_checklist_format(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $user = User::where('email', 'karyawan@test.com')->first();
        $testDate = '2026-10-26';

        // 1. Simpan laporan pagi
        $this->actingAs($admin)->post(route('reports.daily-tasks.store'), [
            'user_id' => $user->id,
            'date' => $testDate,
            'morning_planned_tasks' => "Fitur absensi RFID\nTesting printer thermal\nDeploy staging server",
        ]);

        $report = \App\Models\DailyReport::where('user_id', $user->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($report);
        $this->assertEquals('MORNING_ONLY', $report->status);

        // 2. Update via Checklist: Task 0 & 2 dicentang (selesai), Task 1 TIDAK dicentang (pending)
        // Ditambah task tambahan yang diketik
        $updateResponse = $this->actingAs($admin)->put(route('reports.daily-tasks.update', $report), [
            'all_morning_tasks' => [
                'Fitur absensi RFID',
                'Testing printer thermal',
                'Deploy staging server',
            ],
            'completed_task_indices' => ['0', '2'], // Task 1 unchecked
            'evening_additional_tasks' => 'Perbaikan bug koneksi RFID',
            'additional_tasks_checklist' => [
                'Perbaikan bug koneksi RFID',
            ],
            'completed_additional_indices' => ['0'],
        ]);

        $updateResponse->assertRedirect();

        $report->refresh();
        $this->assertEquals('COMPLETED', $report->status);

        // Task 0 & 2 harus masuk di completed
        $this->assertStringContainsString('Fitur absensi RFID', $report->evening_completed_tasks);
        $this->assertStringContainsString('Deploy staging server', $report->evening_completed_tasks);
        $this->assertStringContainsString('[Tambahan] Perbaikan bug koneksi RFID', $report->evening_completed_tasks);

        // Task 1 yang tidak dicentang otomatis masuk di pending
        $this->assertStringNotContainsString('Testing printer thermal', $report->evening_completed_tasks);
        $this->assertStringContainsString('Testing printer thermal', $report->evening_pending_tasks);

        // Task tambahan tetap tersimpan di kolomnya
        $this->assertStringContainsString('Perbaikan bug koneksi RFID', $report->evening_additional_tasks);
    }

    public function test_can_store_daily_task_with_immediate_evening_checklist(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $user = User::where('email', 'karyawan@test.com')->first();
        $testDate = '2026-10-27';

        // Simpan langsung dengan checklist sore aktif
        $storeResponse = $this->actingAs($admin)->post(route('reports.daily-tasks.store'), [
            'user_id' => $user->id,
            'date' => $testDate,
            'morning_planned_tasks' => "Task A\nTask B",
            'with_evening_checklist' => '1',
            'all_morning_tasks' => ['Task A', 'Task B'],
            'completed_task_indices' => ['0'], // Task A checked, Task B unchecked
            'evening_additional_tasks' => 'Task Tambahan C',
            'additional_tasks_checklist' => ['Task Tambahan C'],
            'completed_additional_indices' => ['0'],
        ]);

        $storeResponse->assertRedirect();

        $report = \App\Models\DailyReport::where('user_id', $user->id)->whereDate('date', $testDate)->first();
        $this->assertNotNull($report);
        $this->assertEquals('COMPLETED', $report->status);
        $this->assertStringContainsString('Task A', $report->evening_completed_tasks);
        $this->assertStringContainsString('Task B', $report->evening_pending_tasks);
        $this->assertStringContainsString('[Tambahan] Task Tambahan C', $report->evening_completed_tasks);
        $this->assertStringContainsString('Task Tambahan C', $report->evening_additional_tasks);
    }

    public function test_user_can_download_slip_gaji_pdf(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $payroll = \App\Models\Payroll::first();

        if (!$payroll) {
            $payroll = \App\Models\Payroll::create([
                'user_id' => $admin->id,
                'month' => 10,
                'year' => 2026,
                'total_working_days' => 22,
                'total_present_onsite' => 20,
                'total_wfh_days' => 2,
                'total_overtime_hours' => 5,
                'daily_salary_rate' => 350000,
                'wfh_deduction_amount' => 70000,
                'overtime_pay' => 105000,
                'gross_salary' => 7805000,
                'net_salary' => 7735000,
                'status' => 'PUBLISHED',
            ]);
        }

        $response = $this->actingAs($admin)->get(route('reports.payrolls.pdf', $payroll->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_can_download_payslip_pdf_with_detailed_wfh_and_leave_deductions(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $employee = User::firstOrCreate(
            ['email' => 'karyawan.detail@example.com'],
            [
                'name' => 'Karyawan Detail',
                'password' => bcrypt('password'),
                'employment_type' => 'KARTAP',
                'basic_salary' => 5000000,
            ]
        );

        $payroll = \App\Models\Payroll::create([
            'user_id' => $employee->id,
            'month' => 10,
            'year' => 2026,
            'total_working_days' => 22,
            'total_present_onsite' => 18,
            'total_wfh_days' => 2,
            'total_sick_days' => 1,
            'total_permit_days' => 1,
            'total_overtime_hours' => 0,
            'daily_salary_rate' => 227272.73,
            'wfh_deduction_amount' => 45454.55,
            'leave_deduction_amount' => 454545.45,
            'overtime_pay' => 0,
            'gross_salary' => 5000000,
            'net_salary' => 4500000,
            'status' => 'PUBLISHED',
        ]);

        $response = $this->actingAs($admin)->get(route('reports.payrolls.pdf', $payroll->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
