<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Usage di route: ->middleware('role:admin,koordinator')
     * Artinya: hanya role admin ATAU koordinator yang boleh akses
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return $this->forbidden('Token tidak valid atau sudah kadaluarsa.');
        }

        if (!$user) {
            return $this->forbidden('User tidak ditemukan.');
        }

        if (!$user->is_active) {
            return $this->forbidden('Akun Anda dinonaktifkan.');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return $this->forbidden(
                'Anda tidak memiliki akses ke fitur ini. ' .
                'Diperlukan role: ' . implode(' atau ', $roles)
            );
        }

        // Inject user ke request agar bisa diakses di controller
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }

    private function forbidden(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], 403);
    }
}