<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebPortalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_super_admin_can_login_and_access_admin_portal(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $user = User::where('email', 'admin@test.com')->first();
        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.payroll.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.incentives.index'))->assertOk();
    }

    public function test_ceo_can_login_and_access_executive_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'ceo@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('executive.dashboard'));

        $user = User::where('email', 'ceo@test.com')->first();
        $this->assertFalse($user->isAttendanceRequired());

        $dashboardResponse = $this->actingAs($user)->get(route('executive.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('Analitik Keuangan & Pengeluaran Gaji Bulanan', false);
        $dashboardResponse->assertSee('Analitik Kedisiplinan & Kehadiran', false);
        $dashboardResponse->assertSee('Evaluasi Produktivitas & Tugas Harian', false);
        $dashboardResponse->assertSee('Pusat Laporan Komprehensif Direksi', false);

        // CEO dapat mengakses seluruh laporan komprehensif dan bebas kewajiban laporan tugas harian
        $dailyTaskResponse = $this->actingAs($user)->get(route('reports.daily-tasks'));
        $dailyTaskResponse->assertOk();
        $dailyTaskResponse->assertViewHas('users', function ($users) {
            return !$users->pluck('email')->contains('ceo@test.com');
        });
    }

    public function test_finance_can_login_and_access_payroll_portal(): void
    {
        $response = $this->post('/login', [
            'email' => 'finance@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('finance.payroll.index'));

        $user = User::where('email', 'finance@test.com')->first();
        $this->actingAs($user)->get(route('finance.compensations.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.payroll.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.incentives.index'))->assertOk();

        // Finance dapat memperbarui gaji pokok dan rekening bank pegawai
        $employee = User::where('email', 'karyawan@test.com')->first();
        $this->actingAs($user)->put(route('finance.compensations.update', $employee), [
            'basic_salary' => 5500000,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1440099887766',
            'bank_account_holder' => 'Andi Staff Pratama',
        ])->assertRedirect();

        $employee->refresh();
        $this->assertEquals(5500000, (float) $employee->basic_salary);
        $this->assertEquals('Bank Mandiri', $employee->bank_name);
        $this->assertEquals('1440099887766', $employee->bank_account_number);
        $this->assertEquals('Andi Staff Pratama', $employee->bank_account_holder);
    }

    public function test_karyawan_and_intern_are_denied_web_access_even_with_approval_flag(): void
    {
        foreach (['karyawan@test.com', 'intern@test.com', 'manager@test.com'] as $email) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);

            $response->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_web_role_with_approval_permission_can_access_approvals(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $this->actingAs($admin)->get(route('approval.index'))->assertOk();

        $finance = User::where('email', 'finance@test.com')->first();
        $this->actingAs($finance)->get(route('approval.index'))->assertOk();
    }

    public function test_approver_can_approve_leave_and_auto_generate_attendances(): void
    {
        $approver = User::where('email', 'manager@test.com')->first();
        $employee = User::where('email', 'karyawan@test.com')->first();

        $leave = \App\Models\LeaveRequest::create([
            'user_id' => $employee->id,
            'type' => 'SAKIT',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-21',
            'reason' => 'Demam tinggi',
            'status' => 'PENDING',
        ]);

        $this->actingAs($approver)
            ->patch(route('approval.approve', $leave))
            ->assertRedirect();

        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'APPROVED',
            'approved_by' => $approver->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => '2026-10-20',
            'status' => 'SAKIT',
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => '2026-10-21',
            'status' => 'SAKIT',
        ]);
    }

    public function test_admin_can_create_and_update_user_with_roles(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $role = \App\Models\Role::first();

        // 1. Create employee
        $createResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'password' => 'secret123',
            'rfid_uid' => 'RFID998877',
            'employment_type' => 'KARTAP',
            'basic_salary' => 6000000,
            'can_approve_leaves' => 1,
            'roles' => [$role->id],
        ]);

        $createResponse->assertRedirect(route('admin.users.index'));
        $createdUser = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->roles->contains('id', $role->id));
        $this->assertEquals(6000000, (float) $createdUser->basic_salary);

        // 2. Update employee
        $updateResponse = $this->actingAs($admin)->put(route('admin.users.update', $createdUser), [
            'name' => 'Budi Santoso Updated',
            'email' => 'budi.santoso@example.com',
            'rfid_uid' => 'RFID998877',
            'employment_type' => 'PKWT',
            'basic_salary' => 7500000,
            'can_approve_leaves' => 0,
            'roles' => [$role->id],
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $createdUser->refresh();
        $this->assertEquals('Budi Santoso Updated', $createdUser->name);
        $this->assertEquals('PKWT', $createdUser->employment_type);
        $this->assertEquals(7500000, (float) $createdUser->basic_salary);
        $this->assertFalse((bool) $createdUser->can_approve_leaves);
        $this->assertTrue($createdUser->roles->contains('id', $role->id));
    }

    public function test_admin_can_dynamically_update_app_name_slogan_and_logo_in_settings(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'app_name' => 'NusantaraHR',
            'app_slogan' => 'Solusi SDM & Penggajian Modern',
            'late_tolerance_minutes' => 15,
            'overtime_tolerance_minutes' => 30,
            'wfa_deduction_kartap_pct' => 10,
            'wfa_deduction_pkwt_pct' => 30,
            'payday_date' => 25,
            'office_name' => 'Kantor Pusat Nusantara',
            'office_address' => 'Jl. Jend. Sudirman Kav. 52',
            'office_latitude' => -6.2088,
            'office_longitude' => 106.8456,
            'office_radius_meters' => 100,
        ])->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'app_name', 'value' => 'NusantaraHR']);
        $this->assertDatabaseHas('settings', ['key' => 'app_slogan', 'value' => 'Solusi SDM & Penggajian Modern']);

        // Pastikan nama aplikasi dan slogan baru otomatis tampil di sidebar dasbor & halaman pengaturan
        $dashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Nusantara', false);
        $dashboard->assertSee('Solusi SDM & Penggajian Modern');
    }
}
