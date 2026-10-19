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
    public function index(): View
    {
        $incentives = ProjectIncentive::with('user')
            ->latest('given_date')
            ->paginate(15);

        $interns = User::where('employment_type', 'INTERN')->get();

        return view('finance.incentives.index', compact('incentives', 'interns'));
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

        return back()->with('success', 'Insentif proyek magang berhasil dicatat.');
    }
}
