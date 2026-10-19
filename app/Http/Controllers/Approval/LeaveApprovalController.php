<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveApprovalController extends Controller
{
    public function index(): View
    {
        $pendingLeaves = LeaveRequest::with('user')
            ->where('status', 'PENDING')
            ->latest()
            ->get();

        $historyLeaves = LeaveRequest::with(['user', 'approver'])
            ->whereIn('status', ['APPROVED', 'REJECTED'])
            ->latest('updated_at')
            ->paginate(15);

        return view('approvals.index', compact('pendingLeaves', 'historyLeaves'));
    }

    public function approve(LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->approve(auth()->user());

        return back()->with('success', "Pengajuan {$leaveRequest->type} berhasil disetujui dan data presensi otomatis di-generate.");
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest->reject(auth()->user(), $request->input('rejection_reason'));

        return back()->with('success', 'Pengajuan cuti/izin telah ditolak.');
    }
}
