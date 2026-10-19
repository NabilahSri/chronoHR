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
        $this->actingAs($user)->get(route('executive.dashboard'))->assertOk();
    }

    public function test_finance_can_login_and_access_payroll_portal(): void
    {
        $response = $this->post('/login', [
            'email' => 'finance@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('finance.payroll.index'));

        $user = User::where('email', 'finance@test.com')->first();
        $this->actingAs($user)->get(route('finance.payroll.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.incentives.index'))->assertOk();
    }

    public function test_approver_can_login_and_access_approvals(): void
    {
        $response = $this->post('/login', [
            'email' => 'manager@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('approval.index'));

        $user = User::where('email', 'manager@test.com')->first();
        $this->actingAs($user)->get(route('approval.index'))->assertOk();
    }

    public function test_regular_employee_is_denied_web_access(): void
    {
        $response = $this->post('/login', [
            'email' => 'karyawan@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
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
}
