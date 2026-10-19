<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $this->authService->authenticateWeb($request->validated());
        $user = Auth::user();

        $message = "Login berhasil! Selamat datang kembali, {$user->name}.";

        if ($user->hasRole('ceo')) {
            return redirect()->route('executive.dashboard')->with('success', $message);
        } elseif ($user->hasRole('finance')) {
            return redirect()->route('finance.payroll.index')->with('success', $message);
        } elseif ($user->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard')->with('success', $message);
        }
        return redirect()->route('approval.index')->with('success', $message);
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logoutWeb();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
