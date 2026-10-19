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
}
