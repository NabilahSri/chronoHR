<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek Bearer Token dari Header Authorization (Kompatibel penuh dengan Laravel Sanctum)
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            $accessToken = PersonalAccessToken::findToken($bearerToken);

            if (!$accessToken || !($accessToken->tokenable instanceof User)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token autentikasi tidak valid atau telah kedaluwarsa.',
                ], 401);
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Token autentikasi telah kedaluwarsa.',
                ], 401);
            }

            $accessToken->forceFill(['last_used_at' => now()])->save();

            $user = $accessToken->tokenable;
            if (!$user->canAccessMobileApp()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Aplikasi ChronoHR Mobile hanya untuk Karyawan Regular dan Magang.',
                ], 403);
            }

            $request->attributes->set('current_access_token', $accessToken);
            Auth::setUser($user);
            $request->setUserResolver(fn () => $user);

            return $next($request);
        }

        // 2. Fallback untuk pengujian internal (actingAs)
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak terautentikasi. Sertakan header Authorization: Bearer <token>.',
        ], 401);
    }
}
