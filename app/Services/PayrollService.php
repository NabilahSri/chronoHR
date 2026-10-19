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
        $totalIncentives = (float) ProjectIncentive::where('user_id', $user->id)
            ->whereMonth('given_date', $month)
            ->whereYear('given_date', $year)
            ->sum('amount');

        // Uang saku  dari perusahaan (bisa Rp 0 atau nominal sukarela)
        $stipend = (float) ($user->basic_salary ?? 0);
        $totalNet = $stipend + $totalIncentives;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_present_onsite' => 0,
            'total_wfh_days' => 0,
            'total_sick_days' => 0,
            'total_permit_days' => 0,
            'total_overtime_hours' => 0,
            'daily_salary_rate' => 0,
            'wfh_deduction_amount' => 0,
            'leave_deduction_amount' => 0,
            'overtime_pay' => $totalIncentives,
            'gross_salary' => $totalNet,
            'net_salary' => $totalNet,
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
        $totalSick = $attendances->where('status', 'SAKIT')->count();
        $totalPermit = $attendances->where('status', 'IZIN')->count();
        $totalAlpha = $attendances->where('status', 'ALPHA')->count();
        $totalOvertime = (int) $attendances->sum('overtime_hours');

        // Pastikan juga leave_requests yang disetujui terhitung jika belum ada record di tabel presensi
        $approvedLeaves = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('status', 'APPROVED')
            ->where(function ($q) use ($month, $year) {
                $q->where(function ($sub) use ($month, $year) {
                    $sub->whereMonth('start_date', $month)->whereYear('start_date', $year);
                })->orWhere(function ($sub) use ($month, $year) {
                    $sub->whereMonth('end_date', $month)->whereYear('end_date', $year);
                });
            })
            ->get();

        foreach ($approvedLeaves as $leave) {
            $period = \Carbon\CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                if ($date->month == $month && $date->year == $year) {
                    $dateStr = $date->toDateString();
                    $existing = $attendances->first(function ($att) use ($dateStr) {
                        return (is_string($att->date) ? $att->date : $att->date->toDateString()) === $dateStr;
                    });
                    if (!$existing) {
                        if ($leave->type === 'SAKIT') {
                            $totalSick++;
                        } elseif ($leave->type === 'IZIN') {
                            $totalPermit++;
                        } elseif ($leave->type === 'WFH') {
                            $totalWfh++;
                        }
                    }
                }
            }
        }

        $basicSalary = (float) $user->basic_salary;
        $dailyRate = $totalWorkingDays > 0 ? ($basicSalary / $totalWorkingDays) : 0;

        $kartapPct = (float) Setting::getValue('wfh_deduction_kartap_pct', 10) / 100;
        $pkwtPct = (float) Setting::getValue('wfh_deduction_pkwt_pct', 30) / 100;

        $wfhDeductionPct = ($user->employment_type === 'PKWT')
            ? $pkwtPct
            : $kartapPct;

        $wfhDeduction = $totalWfh * ($dailyRate * $wfhDeductionPct);

        // Kebijakan: Sakit atau Izin full tidak dibayar hari itu (100% potongan tarif harian per hari sakit/izin)
        $unpaidLeaveDays = $totalSick + $totalPermit + $totalAlpha;
        $leaveDeduction = $unpaidLeaveDays * $dailyRate;

        $overtimePay = $totalOvertime * self::OVERTIME_RATE_PER_HOUR;
        $netSalary = max(0, $basicSalary - $wfhDeduction - $leaveDeduction + $overtimePay);
        $grossSalary = $basicSalary + $overtimePay;

        return [
            'total_working_days' => $totalWorkingDays,
            'total_present_onsite' => $totalOnsite,
            'total_wfh_days' => $totalWfh,
            'total_sick_days' => $totalSick,
            'total_permit_days' => $totalPermit,
            'total_overtime_hours' => $totalOvertime,
            'daily_salary_rate' => round($dailyRate, 2),
            'wfh_deduction_amount' => round($wfhDeduction, 2),
            'leave_deduction_amount' => round($leaveDeduction, 2),
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
