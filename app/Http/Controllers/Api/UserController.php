<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // GET /api/users — List semua user
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter opsional
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('wilayah_tugas')) {
            $query->where('wilayah_tugas', 'like', '%' . $request->wilayah_tugas . '%');
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->orderBy('name')
                       ->paginate($request->get('per_page', 15));

        return $this->successResponse('Daftar user', $users);
    }

    // POST /api/users — Buat user baru (oleh admin)
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100',
            'username'      => 'required|string|max:50|unique:users|alpha_dash',
            'email'         => 'nullable|email|unique:users',
            'password'      => 'required|string|min:8',
            'role'          => 'required|in:petugas,koordinator,admin,pimpinan',
            'nip'           => 'nullable|string|max:30',
            'phone'         => 'nullable|string|max:20',
            'wilayah_tugas' => 'nullable|string|max:100',
            'posisi' => 'nullable|in:pml,taskforce,subject_matter',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => strtolower($request->username),
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'nip'           => $request->nip,
            'phone'         => $request->phone,
            'wilayah_tugas' => $request->wilayah_tugas,
            'posisi' => $request->posisi,
        ]);

        return $this->successResponse('User berhasil dibuat', $user, 201);
    }

    // GET /api/users/{id}
    public function show(int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        return $this->successResponse('Detail user', $user);
    }

    // PUT /api/users/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'sometimes|string|max:100',
            'email'         => 'sometimes|nullable|email|unique:users,email,' . $id,
            'password'      => 'sometimes|string|min:8',
            'role'          => 'sometimes|in:petugas,koordinator,admin,pimpinan',
            'nip'           => 'sometimes|nullable|string|max:30',
            'phone'         => 'sometimes|nullable|string|max:20',
            'wilayah_tugas' => 'sometimes|nullable|string|max:100',
            'posisi' => 'nullable|in:pml,taskforce,subject_matter',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $data = $request->only(['name', 'email', 'role', 'nip', 'phone', 'wilayah_tugas','posisi']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return $this->successResponse('User berhasil diupdate', $user->fresh());
    }

    // PATCH /api/users/{id}/toggle-active — Aktifkan/nonaktifkan user
    public function toggleActive(int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return $this->successResponse("User berhasil {$status}", [
            'id'        => $user->id,
            'username'  => $user->username,
            'is_active' => $user->is_active,
        ]);
    }

    // DELETE /api/users/{id} — Soft delete
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User tidak ditemukan', 404);
        }

        $user->delete(); // soft delete karena model pakai SoftDeletes

        return $this->successResponse('User berhasil dihapus');
    }

    // ── Helpers ──
    private function successResponse(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    private function errorResponse(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }
}