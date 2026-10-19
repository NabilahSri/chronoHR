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

    public function test_calculate_intern_with_voluntary_stipend_and_project_incentives(): void
    {
        $intern = User::create([
            'name' => 'Intern Uang Saku',
            'email' => 'intern.stipend@example.com',
            'password' => bcrypt('password'),
            'employment_type' => 'INTERN',
            'basic_salary' => 500000, // Uang saku 
        ]);

        ProjectIncentive::create([
            'user_id' => $intern->id,
            'project_name' => 'Proyek Pegang Klien X',
            'amount' => 1000000,
            'given_date' => '2026-10-18',
        ]);

        $payroll = $this->service->calculateForUser($intern, 10, 2026, 22);

        // Uang saku 500k + Proyek 1000k = 1500k, tanpa potongan WFH
        $this->assertEquals(1500000, $payroll['net_salary']);
        $this->assertEquals(1500000, $payroll['gross_salary']);
        $this->assertEquals(0, $payroll['wfh_deduction_amount']);
        $this->assertEquals(1000000, $payroll['overtime_pay']);
    }

    public function test_calculate_employee_payroll_fully_deducts_sick_and_permit_days(): void
    {
        $employee = User::create([
            'name' => 'Karyawan Sakit Izin Test',
            'email' => 'sakit.izin@example.com',
            'password' => bcrypt('password'),
            'employment_type' => 'KARTAP',
            'basic_salary' => 4400000, // Daily rate = 4400000 / 22 = 200,000 / day
        ]);

        // 1 hari Sakit
        Attendance::create([
            'user_id' => $employee->id,
            'date' => '2026-10-05',
            'status' => 'SAKIT',
            'method' => 'FORM',
        ]);

        // 2 hari Izin
        Attendance::create([
            'user_id' => $employee->id,
            'date' => '2026-10-06',
            'status' => 'IZIN',
            'method' => 'FORM',
        ]);
        Attendance::create([
            'user_id' => $employee->id,
            'date' => '2026-10-07',
            'status' => 'IZIN',
            'method' => 'FORM',
        ]);

        $payroll = $this->service->calculateForUser($employee, 10, 2026, 22);

        $this->assertEquals(200000, $payroll['daily_salary_rate']);
        $this->assertEquals(1, $payroll['total_sick_days']);
        $this->assertEquals(2, $payroll['total_permit_days']);
        // Full ga dibayar hari itu: 3 hari x Rp 200.000 = Rp 600.000
        $this->assertEquals(600000, $payroll['leave_deduction_amount']);
        // Net: 4400000 - 600000 = 3800000
        $this->assertEquals(3800000, $payroll['net_salary']);
    }
}
