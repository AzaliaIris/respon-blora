<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorized('User tidak ditemukan.');
            }

            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan oleh admin.',
                    'data'    => null,
                ], 403);
            }

        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Token sudah kadaluarsa. Silakan login ulang.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Token tidak valid.');
        } catch (JWTException $e) {
            return $this->unauthorized('Token tidak ditemukan.');
        }

        return $next($request);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], 401);
    }
}