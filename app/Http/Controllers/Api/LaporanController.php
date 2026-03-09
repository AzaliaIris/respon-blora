<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\LaporanFoto;
use App\Services\NomorTiketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LaporanController extends Controller
{
    public function __construct(
        private NomorTiketService $tiketService
    ) {}

    // ─────────────────────────────────────────────
    // GET /api/laporan
    // Petugas: hanya laporan milik sendiri
    // Admin/Koordinator/Pimpinan: semua laporan
    // ─────────────────────────────────────────────
    public function index(Request $request): JsonResponse
    {
        $user  = JWTAuth::parseToken()->authenticate();
        $query = Laporan::with(['petugas:id,name,username,wilayah_tugas', 'foto']);

        // Petugas hanya lihat laporan sendiri
        if ($user->role === 'petugas') {
            $query->where('user_id', $user->id);
        }

        // Filter opsional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', 'like', '%' . $request->kecamatan . '%');
        }
        if ($request->filled('jenis_kendala')) {
            $query->where('jenis_kendala', $request->jenis_kendala);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_laporan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_laporan', '<=', $request->tanggal_sampai);
        }

        $laporan = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 10));

        return $this->successResponse('Daftar laporan', $laporan);
    }

    // ─────────────────────────────────────────────
    // POST /api/laporan
    // Petugas, Koordinator, Admin bisa buat laporan
    // ─────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        // return response()->json([
        //     'all_data'  => $request->all(),
        //     'has_file'  => $request->hasFile('foto'),
        //     'files_raw' => $request->files->all(), // <-- tambah ini
        //     'foto_type' => gettype($request->file('foto')),
        //     'foto_0'    => $request->file('foto.0') 
        //                 ? get_class($request->file('foto.0')) 
        //                 : 'null',
        // ]);

        $validator = Validator::make($request->all(), [
            'nama_usaha'             => 'required|string|max:200',
            'nama_pemilik'           => 'nullable|string|max:100',
            'alamat_usaha'           => 'required|string',
            'kecamatan'              => 'required|string|max:100',
            'desa_kelurahan'         => 'nullable|string|max:100',
            'latitude'               => 'nullable|numeric|between:-90,90',
            'longitude'              => 'nullable|numeric|between:-180,180',
            'jenis_kendala'          => 'required|in:menolak_diwawancara,tidak_ditemui,alasan_privasi,usaha_tutup,responden_pindah,tidak_ada_waktu,lainnya',
            'jenis_kendala_lainnya'  => 'required_if:jenis_kendala,lainnya|nullable|string|max:200',
            'kronologi'              => 'required|string|min:20|max:2000',
            'foto'                   => 'nullable|array|max:5',
            // 'foto.*' => [
            //     'file',
            //     'max:5120',
            //     function ($attribute, $value, $fail) {
            //         $extension = strtolower($value->getClientOriginalExtension());
            //         $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            //         if (!in_array($extension, $allowed)) {
            //             $fail("The {$attribute} must be a file of type: jpg, jpeg, png, webp.");
            //         }
            //     }
            // ],
            'foto.*' => [
                'file',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    if (!in_array($extension, $allowed)) {
                        $fail("The {$attribute} must be a file of type: jpg, jpeg, png, webp.");
                    }
                    if ($value->getSize() > 5120 * 1024) {
                        $fail("The {$attribute} may not be greater than 5MB.");
                    }
                }
            ],
        ]);

        // return response()->json([
        //     'fails' => $validator->fails(),
        //     'errors' => $validator->errors(),
        //     'foto_files' => collect($request->file('foto'))->map(fn($f) => [
        //         'name'      => $f->getClientOriginalName(),
        //         'mime'      => $f->getMimeType(),
        //         'extension' => $f->getClientOriginalExtension(),
        //         'size'      => $f->getSize(),
        //         'valid'     => $f->isValid(),
        //     ])
        // ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $user = JWTAuth::parseToken()->authenticate();

        try {
            DB::beginTransaction();

            // Generate nomor tiket
            $nomorTiket = $this->tiketService->generate();

            // Simpan laporan
            $laporan = Laporan::create([
                'nomor_tiket'            => $nomorTiket,
                'user_id'                => $user->id,
                'nama_usaha'             => $request->nama_usaha,
                'nama_pemilik'           => $request->nama_pemilik,
                'alamat_usaha'           => $request->alamat_usaha,
                'kecamatan'              => $request->kecamatan,
                'desa_kelurahan'         => $request->desa_kelurahan,
                'latitude'               => $request->latitude,
                'longitude'              => $request->longitude,
                'jenis_kendala'          => $request->jenis_kendala,
                'jenis_kendala_lainnya'  => $request->jenis_kendala_lainnya,
                'kronologi'              => $request->kronologi,
                'status'                 => 'menunggu',
                'tanggal_laporan'        => now(),
            ]);

            //Upload foto jika ada
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $index => $file) {
                    $path = $file->store(
                        'laporan/' . now()->format('Y/m'),
                        'public'
                    );

                    LaporanFoto::create([
                        'laporan_id'      => $laporan->id,
                        'path_foto'       => $path,
                        'nama_file_asli'  => $file->getClientOriginalName(),
                        'latitude'        => $request->input("foto_latitude.{$index}"),
                        'longitude'       => $request->input("foto_longitude.{$index}"),
                        'urutan'          => $index + 1,
                    ]);
                }
            }
            // if ($request->hasFile('foto')) {
            //     foreach ($request->file('foto') as $index => $file) {
            //         // Ambil ekstensi dari nama file asli, bukan dari MIME
            //         $extension = strtolower($file->getClientOriginalExtension());
            //         $filename  = \Str::random(40) . '.' . $extension;
            //         $directory = 'laporan/' . now()->format('Y/m');

            //         // Simpan dengan nama + ekstensi yang benar
            //         $path = $file->storeAs($directory, $filename, 'public');

            //         LaporanFoto::create([
            //             'laporan_id'     => $laporan->id,
            //             'path_foto'      => $path,
            //             'nama_file_asli' => $file->getClientOriginalName(),
            //             'latitude'       => $request->input("foto_latitude.{$index}"),
            //             'longitude'      => $request->input("foto_longitude.{$index}"),
            //             'urutan'         => $index + 1,
            //         ]);
            //     }
            // }

            DB::commit();

            $laporan->load(['foto', 'petugas:id,name,username']);

            return $this->successResponse(
                'Laporan berhasil dikirim. Nomor tiket Anda: ' . $nomorTiket,
                $laporan,
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menyimpan laporan: ' . $e->getMessage(), 500);
        }
    }

    // ─────────────────────────────────────────────
    // GET /api/laporan/{id}
    // ─────────────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        $user    = JWTAuth::parseToken()->authenticate();
        $laporan = Laporan::with([
            'petugas:id,name,username,nip,phone,wilayah_tugas',
            'verifikator:id,name,username',
            'foto',
            'tindakLanjut.petugas:id,name,username',
        ])->find($id);

        if (!$laporan) {
            return $this->errorResponse('Laporan tidak ditemukan', 404);
        }

        // Petugas hanya boleh lihat laporan milik sendiri
        if ($user->role === 'petugas' && $laporan->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak mengakses laporan ini', 403);
        }

        // Tambahkan label ke response
        $data               = $laporan->toArray();
        $data['jenis_kendala_label'] = $laporan->jenis_kendala_label;

        return $this->successResponse('Detail laporan', $data);
    }

    // ─────────────────────────────────────────────
    // PATCH /api/laporan/{id}/verifikasi
    // Hanya Admin & Koordinator
    // ─────────────────────────────────────────────
    public function verifikasi(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'arahan_tindak_lanjut' => 'required|in:ke_pml,ke_taskforce,ke_subject_matter',
            'catatan_admin'        => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $laporan = Laporan::find($id);
        if (!$laporan) {
            return $this->errorResponse('Laporan tidak ditemukan', 404);
        }

        if ($laporan->status !== 'menunggu') {
            return $this->errorResponse(
                'Laporan ini sudah diverifikasi sebelumnya. Status: ' . $laporan->status,
                409
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        $laporan->update([
            'status'                => 'diverifikasi',
            'arahan_tindak_lanjut'  => $request->arahan_tindak_lanjut,
            'catatan_admin'         => $request->catatan_admin,
            'tanggal_verifikasi'    => now(),
            'diverifikasi_oleh'     => $user->id,
        ]);

        return $this->successResponse('Laporan berhasil diverifikasi', $laporan->fresh());
    }

    // ─────────────────────────────────────────────
    // POST /api/laporan/{id}/tindak-lanjut
    // Petugas, Koordinator, Admin
    // ─────────────────────────────────────────────
    public function tindakLanjut(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'hasil'             => 'required|in:berhasil_didata,tetap_menolak,akan_dikunjungi_ulang',
            'keterangan'        => 'required|string|min:10|max:1000',
            'tanggal_kunjungan' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $laporan = Laporan::find($id);
        if (!$laporan) {
            return $this->errorResponse('Laporan tidak ditemukan', 404);
        }

        if (!in_array($laporan->status, ['diverifikasi', 'ditindaklanjuti'])) {
            return $this->errorResponse(
                'Laporan harus sudah diverifikasi sebelum ditindaklanjuti.',
                409
            );
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Petugas hanya bisa tindak lanjuti laporan sendiri
        if ($user->role === 'petugas' && $laporan->user_id !== $user->id) {
            return $this->errorResponse('Anda tidak berhak menindaklanjuti laporan ini', 403);
        }

        DB::beginTransaction();
        try {
            // Simpan riwayat tindak lanjut
            $tindakLanjut = $laporan->tindakLanjut()->create([
                'user_id'           => $user->id,
                'hasil'             => $request->hasil,
                'keterangan'        => $request->keterangan,
                'tanggal_kunjungan' => $request->tanggal_kunjungan,
            ]);

            // Update status laporan berdasarkan hasil
            $statusBaru = match($request->hasil) {
                'berhasil_didata'       => 'selesai',
                'tetap_menolak'         => 'ditutup',
                'akan_dikunjungi_ulang' => 'ditindaklanjuti',
            };

            $updateData = ['status' => $statusBaru];
            if (in_array($statusBaru, ['selesai', 'ditutup'])) {
                $updateData['tanggal_selesai'] = now();
            }

            $laporan->update($updateData);

            DB::commit();

            return $this->successResponse(
                'Tindak lanjut berhasil dicatat. Status laporan: ' . $statusBaru,
                [
                    'tindak_lanjut' => $tindakLanjut,
                    'laporan_status' => $statusBaru,
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal menyimpan tindak lanjut', 500);
        }
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