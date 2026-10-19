<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\ProjectIncentive;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PayrollServiceTest extends TestCase
{
    use DatabaseTransactions;

    private PayrollService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PayrollService();
    }

    public function test_calculate_intern_payroll_sums_project_incentives(): void
    {
        $intern = User::create([
            'name' => 'Intern Test',
            'email' => 'intern.test@example.com',
            'password' => bcrypt('password'),
            'employment_type' => 'INTERN',
            'basic_salary' => 0,
        ]);

        ProjectIncentive::create([
            'user_id' => $intern->id,
            'project_name' => 'Project A',
            'amount' => 500000,
            'given_date' => '2026-10-15',
        ]);

        ProjectIncentive::create([
            'user_id' => $intern->id,
            'project_name' => 'Project B',
            'amount' => 750000,
            'given_date' => '2026-10-20',
        ]);

        $payroll = $this->service->calculateForUser($intern, 10, 2026, 22);

        $this->assertEquals(1250000, $payroll['net_salary']);
        $this->assertEquals(1250000, $payroll['gross_salary']);
        $this->assertEquals('DRAFT', $payroll['status']);
    }

    public function test_calculate_employee_payroll_applies_wfh_deduction_and_overtime(): void
    {
        $employee = User::create([
            'name' => 'Staff Test',
            'email' => 'staff.test@example.com',
            'password' => bcrypt('password'),
            'employment_type' => 'PKWT',
            'basic_salary' => 4400000, // Daily rate = 4400000 / 22 = 200,000
        ]);

        // 1 WFH day -> 30% deduction of daily rate = 60,000
        Attendance::create([
            'user_id' => $employee->id,
            'date' => '2026-10-01',
            'status' => 'WFH',
            'method' => 'FORM',
            'is_late' => false,
            'overtime_hours' => 2, // 2 * 21000 = 42000
        ]);

        $payroll = $this->service->calculateForUser($employee, 10, 2026, 22);

        $this->assertEquals(200000, $payroll['daily_salary_rate']);
        $this->assertEquals(60000, $payroll['wfh_deduction_amount']);
        $this->assertEquals(42000, $payroll['overtime_pay']);
        // Net: 4400000 - 60000 + 42000 = 4382000
        $this->assertEquals(4382000, $payroll['net_salary']);
    }
}
