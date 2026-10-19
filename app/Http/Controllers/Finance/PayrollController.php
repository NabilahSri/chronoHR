<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
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

        $allPayrolls = Payroll::where('month', $month)
            ->where('year', $year)
            ->get();

        $totalExpense = $allPayrolls->sum('net_salary');
        $isPublished = $allPayrolls->where('status', 'PUBLISHED')->isNotEmpty();

        $payrolls = Payroll::with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->paginate(15)
            ->withQueryString();

        return view('finance.payrolls.index', compact('payrolls', 'month', 'year', 'totalExpense', 'isPublished'));
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

        $payrollService->generateForPeriod($month, $year, $totalWorkingDays);

        return redirect()->route('finance.payroll.index', ['month' => $month, 'year' => $year])
            ->with('success', "Draft Payroll periode {$month}/{$year} berhasil dikalkulasi.");
    }

    public function publish(Request $request, PayrollService $payrollService): RedirectResponse
    {
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');

        $payrollService->publish($month, $year);

        return back()->with('success', "Payroll periode {$month}/{$year} berhasil di-publish ke karyawan.");
    }
}
