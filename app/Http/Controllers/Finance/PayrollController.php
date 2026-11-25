<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\Setting;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $userId = $request->input('user_id');
        $employmentType = $request->input('employment_type');
        $status = $request->input('status');

        $query = Payroll::with('user')
            ->where('month', $month)
            ->where('year', $year);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($employmentType) {
            $query->whereHas('user', function ($q) use ($employmentType) {
                $q->where('employment_type', $employmentType);
            });
        }

        if ($status && in_array($status, ['DRAFT', 'PUBLISHED'])) {
            $query->where('status', $status);
        }

        $filteredPayrolls = (clone $query)->get();
        $allPeriodPayrolls = Payroll::where('month', $month)
            ->where('year', $year)
            ->get();

        $totalExpense = (float) $filteredPayrolls->sum('net_salary');
        $isPublished = $allPeriodPayrolls->isNotEmpty()
            && $allPeriodPayrolls->where('status', 'DRAFT')->isEmpty();
        $currentWorkingDays = (int) ($allPeriodPayrolls->first()?->total_working_days ?? 22);
        $calculatedWorkingDays = Holiday::calculateEffectiveWorkingDays($month, $year);
        $currentWorkingDays = (int) ($allPeriodPayrolls->first()?->total_working_days ?? $calculatedWorkingDays);

        $stats = [
            'total_count' => $filteredPayrolls->count(),
            'total_net' => (float) $filteredPayrolls->sum('net_salary'),
            'total_gross' => (float) $filteredPayrolls->sum('gross_salary'),
            'total_wfa_deduction' => (float) $filteredPayrolls->sum('wfa_deduction_amount'),
            'total_leave_deduction' => (float) $filteredPayrolls->sum('leave_deduction_amount'),
            'total_sanction_deduction' => (float) $filteredPayrolls->sum('sanction_deduction_amount'),
            'total_overtime_pay' => (float) $filteredPayrolls->sum('overtime_pay'),
            'published_count' => $filteredPayrolls->where('status', 'PUBLISHED')->count(),
            'draft_count' => $filteredPayrolls->where('status', 'DRAFT')->count(),
        ];

        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $payrolls = $query->orderBy('net_salary', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        $payrollSettings = [
            'payday_date' => Setting::getValue('payday_date', 25),
            'wfa_kartap_pct' => Setting::getValue('wfa_deduction_kartap_pct', 10),
            'wfa_pkwt_pct' => Setting::getValue('wfa_deduction_pkwt_pct', 30),
        ];

        return view('finance.payrolls.index', compact(
            'payrolls',
            'users',
            'month',
            'year',
            'userId',
            'employmentType',
            'status',
            'currentWorkingDays',
            'totalExpense',
            'isPublished',
            'stats',
            'payrollSettings'
        ));
    }

    public function generate(Request $request, PayrollService $payrollService): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2099',
            'total_working_days' => 'nullable|integer|min:1|max:31',
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];
        $totalWorkingDays = (int) ($validated['total_working_days'] ?? 22);
        $totalWorkingDays = (int) ($validated['total_working_days'] ?? Holiday::calculateEffectiveWorkingDays($month, $year));

        $payrollService->generateForPeriod($month, $year, $totalWorkingDays);

        return redirect()->route('finance.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Konsep penggajian periode {$month}/{$year} berhasil dikalkulasi.");
    }

    public function publish(Request $request, PayrollService $payrollService): RedirectResponse
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        $payrollService->publish($month, $year);

        return back()->with('success', "Slip gaji periode {$month}/{$year} berhasil diterbitkan ke karyawan.");
    }
}
