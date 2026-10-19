<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function authenticateWeb(array $credential): bool
    {
        if (!Auth::attempt($credential)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.'
            ]);
        }
        $user = Auth::user();
        $hasWebAccess = $user->hasRole('super_admin')
            || $user->hasRole('ceo')
            || $user->hasRole('finance')
            || $user->can_approve_leaves;
        if (!$hasWebAccess) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akses ditolak. akun Karyawan hanya dapat digunakan melalui Aplikasi Mobile.'
            ]);
        }
        session()->regenerate();
        return true;
    }

    public function logoutWeb(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
