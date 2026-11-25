<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeCompensationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->input('user_id');
        $employmentType = $request->input('employment_type');
        $bankStatus = $request->input('bank_status');
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $query = User::with('roles')->orderBy('name');

        if ($userId) {
            $query->where('id', $userId);
        }

        if ($employmentType) {
            $query->where('employment_type', $employmentType);
        }

        if ($bankStatus === 'REGISTERED') {
            $query->whereNotNull('bank_account_number')
                ->where('bank_account_number', '!=', '');
        } elseif ($bankStatus === 'UNREGISTERED') {
            $query->where(function ($q) {
                $q->whereNull('bank_account_number')
                    ->orWhere('bank_account_number', '');
            });
        }

        $allMatching = (clone $query)->get();
        $stats = [
            'total_employees' => $allMatching->count(),
            'total_basic_salary' => (float) $allMatching->whereIn('employment_type', ['KARTAP', 'PKWT'])->sum('basic_salary'),
            'bank_registered' => $allMatching->filter(fn (User $u) => !empty($u->bank_account_number))->count(),
            'bank_unregistered' => $allMatching->filter(fn (User $u) => empty($u->bank_account_number))->count(),
        ];

        $employees = $query->paginate($perPage)->withQueryString();
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email', 'employment_type']);

        return view('finance.compensations.index', compact(
            'employees',
            'allUsers',
            'stats',
            'userId',
            'employmentType',
            'bankStatus'
        ));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return back()->with('success', "Data gaji pokok dan rekening {$user->name} berhasil diperbarui.");
    }
}
