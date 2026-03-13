<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\LaporanFoto;
use App\Services\NomorTiketService;
use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LaporanMitraController extends Controller
{
    public function __construct(
        private NomorTiketService $tiketService
    ) {}

    /**
     * POST /api/laporan/mitra
     * Form publik — tidak perlu login
     * Keamanan: rate limiting, validasi ketat, sanitasi input
     */
    public function store(Request $request): JsonResponse
    {
        // ── 1. Rate Limiting ──
        // Maksimal 5 request per 10 menit per IP
        $ip  = $request->ip();
        $key = 'laporan_mitra:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Log::warning('Rate limit hit on laporan mitra', ['ip' => $ip]);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ], 429)->header('Retry-After', $seconds);
        }

        RateLimiter::hit($key, 600); // 10 menit window

        // ── 2. Validasi Input (server-side) ──
        $validator = Validator::make($request->all(), [
            // Identitas mitra
            'nama_mitra'             => 'required|string|min:2|max:100',
            'id_mitra'               => 'required|string|min:2|max:50',
            'phone_mitra'            => ['required','string','max:15','regex:/^(\+62|62|0)[0-9]{8,13}$/'],
            'ketua_tim'              => 'required|string|min:2|max:100',

            // Data responden
            'nama_usaha'             => 'required|string|min:2|max:200',
            'nama_pemilik'           => 'nullable|string|max:100',
            'kecamatan'              => 'required|string|in:Blora,Cepu,Jepon,Randublatung,Kunduran,Ngawen,Bogorejo,Todanan,Japah,Banjarejo,Jati,Jiken,Kedungtuban,Kradenan,Sambong,Tunjungan',
            'desa_kelurahan'         => 'nullable|string|max:100',
            'alamat_usaha'           => 'required|string|min:5|max:500',

            // GPS
            'latitude'               => 'nullable|numeric|between:-90,90',
            'longitude'              => 'nullable|numeric|between:-180,180',

            // Kendala
            'jenis_kendala'          => 'required|in:menolak_diwawancara,tidak_ditemui,lainnya',
            'jenis_kendala_lainnya'  => 'required_if:jenis_kendala,lainnya|nullable|string|max:200',

            // Kronologi
            'kronologi'              => 'required|string|min:20|max:2000',

            // Foto
            'foto'                   => 'nullable|array|max:5',
            'foto.*'                 => [
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],
        ], [
            'phone_mitra.regex'      => 'Format nomor HP tidak valid.',
            'jenis_kendala.in'       => 'Jenis kendala tidak valid.',
            'kecamatan.in'           => 'Kecamatan tidak valid.',
            'kronologi.min'          => 'Kronologi minimal 20 karakter.',
        ]);

        if ($validator->fails()) {
            // Log attempt dengan error tapi jangan expose detail ke client
            Log::info('Mitra form validation failed', [
                'ip'     => $ip,
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid. Periksa kembali isian form.',
                // Expose errors hanya untuk field yang aman (bukan server info)
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── 3. Sanitasi Input ──
        // Strip HTML tags dan karakter berbahaya dari semua string input
        $sanitized = collect($validator->validated())->map(function ($value, $key) {
            if (is_string($value)) {
                // Strip HTML, PHP tags, dan trim
                $value = strip_tags($value);
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
                $value = trim($value);
            }
            return $value;
        })->toArray();

        // ── 4. Simpan ke Database ──
        try {
            DB::beginTransaction();

            $nomorTiket = $this->tiketService->generate();

            // Simpan data mitra di kronologi (karena tidak ada user_id)
            // Field tambahan disimpan di kolom khusus atau sebagai JSON di catatan
            $laporan = Laporan::create([
                'nomor_tiket'           => $nomorTiket,
                'user_id'               => null, // laporan dari mitra tanpa akun
                'nama_mitra'            => $sanitized['nama_mitra'],
                'id_mitra'              => $sanitized['id_mitra'],
                'phone_mitra'           => $sanitized['phone_mitra'],
                'ketua_tim'             => $sanitized['ketua_tim'],
                'nama_usaha'            => $sanitized['nama_usaha'],
                'nama_pemilik'          => $sanitized['nama_pemilik'] ?? null,
                'alamat_usaha'          => $sanitized['alamat_usaha'],
                'kecamatan'             => $sanitized['kecamatan'],
                'desa_kelurahan'        => $sanitized['desa_kelurahan'] ?? null,
                'latitude'              => $request->latitude ?? null,
                'longitude'             => $request->longitude ?? null,
                'jenis_kendala'         => $sanitized['jenis_kendala'],
                'jenis_kendala_lainnya' => $sanitized['jenis_kendala_lainnya'] ?? null,
                'kronologi'             => $sanitized['kronologi'],
                'status'                => 'menunggu',
                'sumber'                => 'mitra', // penanda laporan dari form publik
                'tanggal_laporan'       => now(),
            ]);

            // Upload foto
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $index => $file) {
                    // Generate nama file random agar tidak bisa ditebak
                    $ext      = strtolower($file->getClientOriginalExtension());
                    $filename = Str::random(40) . '.' . $ext;
                    $path     = $file->storeAs(
                        'laporan/' . now()->format('Y/m'),
                        $filename,
                        'public'
                    );

                    LaporanFoto::create([
                        'laporan_id'     => $laporan->id,
                        'path_foto'      => $path,
                        'nama_file_asli' => Str::random(16) . '.' . $ext, // jangan simpan nama asli
                        'urutan'         => $index + 1,
                    ]);
                }
            }

            DB::commit();

            // Kirim notifikasi ke admin, pimpinan, koordinator wilayah
            NotifikasiService::laporanBaru($laporan);

            // Log sukses (tanpa data sensitif)
            Log::info('Laporan mitra berhasil', [
                'tiket'     => $nomorTiket,
                'kecamatan' => $laporan->kecamatan,
                'ip'        => $ip,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim.',
                'data'    => [
                    'nomor_tiket' => $nomorTiket,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Laporan mitra gagal disimpan', [
                'ip'    => $ip,
                'error' => $e->getMessage(),
            ]);

            // Jangan expose detail error ke client
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
            ], 500);
        }
    }
}