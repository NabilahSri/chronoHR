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
        if (!$user || !$user->canAccessWebPortal()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akses ditolak. Akun Karyawan Regular dan Magang hanya dapat masuk melalui Aplikasi Mobile ChronoHR.'
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
