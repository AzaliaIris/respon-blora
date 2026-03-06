<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // -----------------------------------------------
    // POST /api/auth/register
    // -----------------------------------------------
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100',
            'username'      => 'required|string|max:50|unique:users|alpha_dash',
            'email'         => 'nullable|email|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|in:petugas,koordinator,admin,pimpinan',
            'nip'           => 'nullable|string|max:30',
            'phone'         => 'nullable|string|max:20',
            'wilayah_tugas' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validasi gagal',
                422,
                $validator->errors()
            );
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => strtolower($request->username),
            'email'         => $request->email,
            'password'      => Hash::make($request->password), // bcrypt otomatis
            'role'          => $request->role,
            'nip'           => $request->nip,
            'phone'         => $request->phone,
            'wilayah_tugas' => $request->wilayah_tugas,
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->successResponse(
            'Registrasi berhasil',
            [
                'user'  => $this->formatUser($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => (int) config('jwt.ttl') * 60,
            ],
            201
        );
    }

    // -----------------------------------------------
    // POST /api/auth/login
    // -----------------------------------------------
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        // Cari user berdasarkan username
        $user = User::where('username', strtolower($request->username))->first();

        // Cek apakah user ada, aktif, dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Username atau password salah', 401);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Akun Anda dinonaktifkan. Hubungi admin.', 403);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return $this->errorResponse('Gagal membuat token. Coba lagi.', 500);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        return $this->successResponse('Login berhasil', [
            'user'       => $this->formatUser($user),
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => (int) config('jwt.ttl') * 60,
        ]);
    }

    // -----------------------------------------------
    // POST /api/auth/logout
    // -----------------------------------------------
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->successResponse('Logout berhasil');
        } catch (JWTException $e) {
            return $this->errorResponse('Gagal logout', 500);
        }
    }

    // -----------------------------------------------
    // GET /api/auth/me
    // -----------------------------------------------
    public function me(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        return $this->successResponse('Data user', $this->formatUser($user));
    }

    // -----------------------------------------------
    // POST /api/auth/refresh
    // -----------------------------------------------
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return $this->successResponse('Token diperbarui', [
                'token'      => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => (int) config('jwt.ttl') * 60,
            ]);
        } catch (JWTException $e) {
            return $this->errorResponse('Token tidak valid atau sudah kadaluarsa', 401);
        }
    }

    // -----------------------------------------------
    // HELPER: Format user output (tanpa password)
    // -----------------------------------------------
    private function formatUser(User $user): array
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'username'      => $user->username,
            'email'         => $user->email,
            'role'          => $user->role,
            'nip'           => $user->nip,
            'phone'         => $user->phone,
            'wilayah_tugas' => $user->wilayah_tugas,
            'last_login_at' => $user->last_login_at?->toDateTimeString(),
        ];
    }

    // -----------------------------------------------
    // HELPER: Consistent API Response
    // -----------------------------------------------
    private function successResponse(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    private function errorResponse(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}