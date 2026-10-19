<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\ProjectIncentive;
use App\Models\Setting;
use App\Models\User;

class PayrollService
{
    private const OVERTIME_RATE_PER_HOUR = 21000;

    public function generateForPeriod(int $month, int $year, int $totalWorkingDays = 22): int
    {
        $users = User::all();

        foreach ($users as $user) {
            $payrollData = $this->calculateForUser($user, $month, $year, $totalWorkingDays);

            Payroll::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'month' => $month,
                    'year' => $year,
                ],
                $payrollData
            );
        }

        return $users->count();
    }

    public function calculateForUser(User $user, int $month, int $year, int $totalWorkingDays): array
    {
        if ($user->employment_type === 'INTERN') {
            return $this->calculateIntern($user, $month, $year, $totalWorkingDays);
        }

        return $this->calculateRegularEmployee($user, $month, $year, $totalWorkingDays);
    }

    private function calculateIntern(User $user, int $month, int $year, int $totalWorkingDays): array
    {
        $totalIncentives = ProjectIncentive::where('user_id', $user->id)
            ->whereMonth('given_date', $month)
            ->whereYear('given_date', $year)
            ->sum('amount');

        return [
            'total_working_days' => $totalWorkingDays,
            'total_present_onsite' => 0,
            'total_wfh_days' => 0,
            'total_overtime_hours' => 0,
            'daily_salary_rate' => 0,
            'wfh_deduction_amount' => 0,
            'overtime_pay' => 0,
            'gross_salary' => $totalIncentives,
            'net_salary' => $totalIncentives,
            'status' => 'DRAFT',
        ];
    }

    private function calculateRegularEmployee(User $user, int $month, int $year, int $totalWorkingDays): array
    {
        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalOnsite = $attendances->where('status', 'HADIR_ON_SITE')->count();
        $totalWfh = $attendances->where('status', 'WFH')->count();
        $totalOvertime = (int) $attendances->sum('overtime_hours');

        $basicSalary = (float) $user->basic_salary;
        $dailyRate = $totalWorkingDays > 0 ? ($basicSalary / $totalWorkingDays) : 0;

        $kartapPct = (float) Setting::getValue('wfh_deduction_kartap_pct', 10) / 100;
        $pkwtPct = (float) Setting::getValue('wfh_deduction_pkwt_pct', 30) / 100;

        $wfhDeductionPct = ($user->employment_type === 'PKWT')
            ? $pkwtPct
            : $kartapPct;

        $wfhDeduction = $totalWfh * ($dailyRate * $wfhDeductionPct);
        $overtimePay = $totalOvertime * self::OVERTIME_RATE_PER_HOUR;
        $netSalary = max(0, $basicSalary - $wfhDeduction + $overtimePay);
        $grossSalary = $basicSalary + $overtimePay;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_present_onsite' => $totalOnsite,
            'total_wfh_days' => $totalWfh,
            'total_overtime_hours' => $totalOvertime,
            'daily_salary_rate' => round($dailyRate, 2),
            'wfh_deduction_amount' => round($wfhDeduction, 2),
            'overtime_pay' => round($overtimePay, 2),
            'gross_salary' => round($grossSalary, 2),
            'net_salary' => round($netSalary, 2),
            'status' => 'DRAFT',
        ];
    }

    public function publish(int $month, int $year): int
    {
        return Payroll::where('month', $month)
            ->where('year', $year)
            ->update(['status' => 'PUBLISHED']);
    }
}
