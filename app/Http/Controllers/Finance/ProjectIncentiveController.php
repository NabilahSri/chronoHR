<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ProjectIncentive;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectIncentiveController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->input('user_id');
        $employmentType = $request->input('employment_type');
        $month = $request->input('month');
        $year = $request->input('year');
        $perPage = in_array((int) $request->input('per_page'), [5, 10, 15, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $query = ProjectIncentive::with('user')->latest('given_date');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($employmentType) {
            $query->whereHas('user', function ($q) use ($employmentType) {
                $q->where('employment_type', $employmentType);
            });
        }

        if ($month) {
            $query->whereMonth('given_date', (int) $month);
        }

        if ($year) {
            $query->whereYear('given_date', (int) $year);
        }

        $allMatching = (clone $query)->get();
        $stats = [
            'total_count' => $allMatching->count(),
            'total_amount' => (float) $allMatching->sum('amount'),
            'intern_amount' => (float) $allMatching->filter(fn ($i) => ($i->user->employment_type ?? '') === 'INTERN')->sum('amount'),
            'regular_amount' => (float) $allMatching->filter(fn ($i) => in_array($i->user->employment_type ?? '', ['KARTAP', 'PKWT']))->sum('amount'),
        ];

        $incentives = $query->paginate($perPage)->withQueryString();

        // Seluruh pegawai (Magang maupun KARTAP/PKWT yang memegang proyek)
        $employees = User::orderByRaw("CASE WHEN employment_type = 'INTERN' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'employment_type']);

        // Variabel $interns tetap disediakan untuk kompatibilitas view lama
        $interns = $employees;

        return view('finance.incentives.index', compact(
            'incentives',
            'interns',
            'employees',
            'stats',
            'userId',
            'employmentType',
            'month',
            'year'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1000',
            'given_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        ProjectIncentive::create($validated);

        return back()->with('success', 'Insentif proyek berhasil dicatat dan akan diakumulasikan pada kalkulasi penggajian.');
    }
}
