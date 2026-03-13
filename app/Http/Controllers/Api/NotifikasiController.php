<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotifikasiController extends Controller
{
    public function index(): JsonResponse
    {
        $user  = JWTAuth::parseToken()->authenticate();
        $notif = Notifikasi::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $notif,
            'unread'  => $notif->where('is_read', false)->count(),
        ]);
    }

    public function markRead(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        Notifikasi::where('id', $id)->where('user_id', $user->id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        Notifikasi::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}