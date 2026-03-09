<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────
    // GET /api/dashboard/ringkasan
    // Kartu statistik utama — semua role manajerial
    // ─────────────────────────────────────────────
    public function ringkasan(Request $request): JsonResponse
    {
        $bulanIni  = now()->month;
        $tahunIni  = now()->year;
        $mingguIni = now()->startOfWeek();

        // ── Total keseluruhan ──
        $totalLaporan = Laporan::count();

        // ── Per status ──
        $perStatus = Laporan::select('status', DB::raw('COUNT(*) as jumlah'))
                            ->groupBy('status')
                            ->pluck('jumlah', 'status')
                            ->toArray();

        // ── Bulan ini ──
        $bulanIniQuery = Laporan::whereMonth('tanggal_laporan', $bulanIni)
                                ->whereYear('tanggal_laporan', $tahunIni);

        // ── Minggu ini ──
        $mingguIniQuery = Laporan::where('tanggal_laporan', '>=', $mingguIni);

        // ── Hitung tingkat penyelesaian ──
        $totalSelesai  = $perStatus['selesai']  ?? 0;
        $totalDitutup  = $perStatus['ditutup']  ?? 0;
        $totalTertutup = $totalSelesai + $totalDitutup;
        $tingkatSelesai = $totalLaporan > 0
            ? round(($totalSelesai / $totalLaporan) * 100, 1)
            : 0;

        // ── Rata-rata waktu penyelesaian (hari) ──
        $rataWaktu = Laporan::whereNotNull('tanggal_selesai')
                            ->whereNotNull('tanggal_laporan')
                            ->select(DB::raw(
                                'ROUND(AVG(DATEDIFF(tanggal_selesai, tanggal_laporan)), 1) as rata_hari'
                            ))
                            ->value('rata_hari');

        return $this->successResponse('Ringkasan dashboard', [
            'total' => [
                'semua'           => $totalLaporan,
                'menunggu'        => $perStatus['menunggu']         ?? 0,
                'diverifikasi'    => $perStatus['diverifikasi']     ?? 0,
                'ditindaklanjuti' => $perStatus['ditindaklanjuti']  ?? 0,
                'selesai'         => $totalSelesai,
                'ditutup'         => $totalDitutup,
            ],
            'periode' => [
                'bulan_ini'  => $bulanIniQuery->count(),
                'minggu_ini' => $mingguIniQuery->count(),
                'hari_ini'   => Laporan::whereDate('tanggal_laporan', today())->count(),
            ],
            'kinerja' => [
                'tingkat_penyelesaian_persen' => $tingkatSelesai,
                'total_kasus_tertutup'        => $totalTertutup,
                'rata_waktu_selesai_hari'     => $rataWaktu ?? 0,
                'kasus_menunggu_lama'         => Laporan::where('status', 'menunggu')
                                                        ->where('tanggal_laporan', '<', now()->subDays(3))
                                                        ->count(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/dashboard/tren-mingguan
    // Data grafik laporan masuk per minggu (8 minggu terakhir)
    // ─────────────────────────────────────────────
    public function trenMingguan(Request $request): JsonResponse
    {
        $minggu = (int) $request->get('minggu', 8); // default 8 minggu
        $minggu = min($minggu, 24); // maksimal 24 minggu

        $data = [];

        for ($i = $minggu - 1; $i >= 0; $i--) {
            $mulai  = now()->subWeeks($i)->startOfWeek();
            $selesai = now()->subWeeks($i)->endOfWeek();

            $row = Laporan::whereBetween('tanggal_laporan', [$mulai, $selesai])
                          ->select('status', DB::raw('COUNT(*) as jumlah'))
                          ->groupBy('status')
                          ->pluck('jumlah', 'status')
                          ->toArray();

            $data[] = [
                'label'          => 'Minggu ' . $mulai->format('d M'),
                'periode_mulai'  => $mulai->toDateString(),
                'periode_selesai'=> $selesai->toDateString(),
                'total'          => array_sum($row),
                'menunggu'       => $row['menunggu']         ?? 0,
                'selesai'        => $row['selesai']          ?? 0,
                'ditutup'        => $row['ditutup']          ?? 0,
                'proses'         => ($row['diverifikasi']    ?? 0)
                                  + ($row['ditindaklanjuti'] ?? 0),
            ];
        }

        return $this->successResponse('Tren laporan mingguan', $data);
    }

    // ─────────────────────────────────────────────
    // GET /api/dashboard/per-kecamatan
    // Sebaran laporan per wilayah kecamatan
    // ─────────────────────────────────────────────
    public function perKecamatan(Request $request): JsonResponse
    {
        $query = Laporan::select(
            'kecamatan',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "menunggu"        THEN 1 ELSE 0 END) as menunggu'),
            DB::raw('SUM(CASE WHEN status = "diverifikasi"    THEN 1 ELSE 0 END) as diverifikasi'),
            DB::raw('SUM(CASE WHEN status = "ditindaklanjuti" THEN 1 ELSE 0 END) as ditindaklanjuti'),
            DB::raw('SUM(CASE WHEN status = "selesai"         THEN 1 ELSE 0 END) as selesai'),
            DB::raw('SUM(CASE WHEN status = "ditutup"         THEN 1 ELSE 0 END) as ditutup'),
            DB::raw('ROUND(
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) * 100.0 / COUNT(*),
            1) as persen_selesai')
        )
        ->groupBy('kecamatan')
        ->orderByDesc('total');

        // Filter tahun jika diberikan
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_laporan', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_laporan', $request->bulan);
        }

        $data = $query->get();

        return $this->successResponse('Rekap per kecamatan', [
            'data'  => $data,
            'total' => $data->sum('total'),
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/dashboard/per-kendala
    // Jenis kendala terbanyak — untuk evaluasi
    // ─────────────────────────────────────────────
    public function perKendala(Request $request): JsonResponse
    {
        $labelMap = [
            'menolak_diwawancara' => 'Menolak Diwawancara',
            'tidak_ditemui'       => 'Tidak Ditemui',
            'alasan_privasi'      => 'Alasan Privasi',
            'usaha_tutup'         => 'Usaha Tutup',
            'responden_pindah'    => 'Responden Pindah',
            'tidak_ada_waktu'     => 'Tidak Ada Waktu',
            'lainnya'             => 'Lainnya',
        ];

        $query = Laporan::select(
            'jenis_kendala',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as berhasil'),
            DB::raw('SUM(CASE WHEN status = "ditutup" THEN 1 ELSE 0 END) as gagal'),
            DB::raw('ROUND(
                SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) * 100.0 / COUNT(*),
            1) as persen_berhasil')
        )
        ->groupBy('jenis_kendala')
        ->orderByDesc('total');

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_laporan', $request->tahun);
        }

        $data = $query->get()->map(function ($item) use ($labelMap) {
            $item->label = $labelMap[$item->jenis_kendala] ?? $item->jenis_kendala;
            return $item;
        });

        $total = $data->sum('total');

        // Tambahkan persentase dari total
        $data = $data->map(function ($item) use ($total) {
            $item->persen_dari_total = $total > 0
                ? round(($item->total / $total) * 100, 1)
                : 0;
            return $item;
        });

        return $this->successResponse('Rekap per jenis kendala', [
            'data'  => $data,
            'total' => $total,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/dashboard/tingkat-selesai
    // Breakdown tingkat penyelesaian detail
    // ─────────────────────────────────────────────
    public function tingkatSelesai(Request $request): JsonResponse
    {
        // Per arahan tindak lanjut
        $perArahan = Laporan::whereNotNull('arahan_tindak_lanjut')
            ->select(
                'arahan_tindak_lanjut',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as selesai'),
                DB::raw('SUM(CASE WHEN status = "ditutup" THEN 1 ELSE 0 END) as ditutup'),
                DB::raw('ROUND(
                    SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) * 100.0 / COUNT(*),
                1) as persen_selesai')
            )
            ->groupBy('arahan_tindak_lanjut')
            ->get()
            ->map(function ($item) {
                $item->label = match($item->arahan_tindak_lanjut) {
                    'ke_pml'            => 'Ke PML',
                    'ke_taskforce'      => 'Ke Taskforce',
                    'ke_subject_matter' => 'Ke Subject Matter',
                    default             => $item->arahan_tindak_lanjut,
                };
                return $item;
            });

        // Tren selesai per bulan (6 bulan terakhir)
        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $row   = Laporan::whereMonth('tanggal_laporan', $bulan->month)
                            ->whereYear('tanggal_laporan', $bulan->year)
                            ->select('status', DB::raw('COUNT(*) as jumlah'))
                            ->groupBy('status')
                            ->pluck('jumlah', 'status')
                            ->toArray();

            $total   = array_sum($row);
            $selesai = $row['selesai'] ?? 0;

            $trenBulanan[] = [
                'bulan'               => $bulan->translatedFormat('F Y'),
                'bulan_key'           => $bulan->format('Y-m'),
                'total'               => $total,
                'selesai'             => $selesai,
                'ditutup'             => $row['ditutup'] ?? 0,
                'persen_penyelesaian' => $total > 0 ? round(($selesai / $total) * 100, 1) : 0,
            ];
        }

        return $this->successResponse('Tingkat penyelesaian', [
            'per_arahan'   => $perArahan,
            'tren_bulanan' => $trenBulanan,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/dashboard/aktivitas-petugas
    // Performa masing-masing petugas
    // ─────────────────────────────────────────────
    public function aktivitasPetugas(Request $request): JsonResponse
    {
        $query = User::where('role', 'petugas')
            ->where('is_active', true)
            ->withCount([
                'laporan as total_laporan',
                'laporan as laporan_selesai' => fn($q) => $q->where('status', 'selesai'),
                'laporan as laporan_menunggu' => fn($q) => $q->where('status', 'menunggu'),
                'laporan as laporan_proses'   => fn($q) => $q->whereIn('status', [
                    'diverifikasi', 'ditindaklanjuti'
                ]),
            ])
            ->select('id', 'name', 'username', 'wilayah_tugas', 'nip');

        // Filter wilayah
        if ($request->filled('kecamatan')) {
            $query->where('wilayah_tugas', 'like', '%' . $request->kecamatan . '%');
        }

        $petugas = $query->orderByDesc('total_laporan')->get();

        // Tambahkan persen selesai per petugas
        $petugas = $petugas->map(function ($user) {
            $user->persen_selesai = $user->total_laporan > 0
                ? round(($user->laporan_selesai / $user->total_laporan) * 100, 1)
                : 0;

            // Laporan terbaru
            $user->laporan_terakhir = \App\Models\Laporan::where('user_id', $user->id)
                ->latest('tanggal_laporan')
                ->value('tanggal_laporan');

            return $user;
        });

        return $this->successResponse('Aktivitas petugas', [
            'data'           => $petugas,
            'total_petugas'  => $petugas->count(),
            'rata_laporan'   => $petugas->count() > 0
                ? round($petugas->avg('total_laporan'), 1)
                : 0,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/laporan/ekspor
    // Export rekap ke CSV — Admin & Pimpinan
    // ─────────────────────────────────────────────
    public function ekspor(Request $request)
    {
        $query = Laporan::with([
            'petugas:id,name,username,nip,wilayah_tugas',
            'verifikator:id,name',
        ]);

        // Filter periode
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_laporan', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_laporan', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        $laporan = $query->orderBy('tanggal_laporan')->get();

        // Generate CSV
        $namaFile = 'rekap_laporan_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$namaFile}\"",
        ];

        $callback = function () use ($laporan) {
            $handle = fopen('php://output', 'w');

            // BOM untuk Excel agar UTF-8 terbaca
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header CSV
            fputcsv($handle, [
                'No', 'Nomor Tiket', 'Tanggal Laporan',
                'Nama Petugas', 'NIP', 'Wilayah Tugas',
                'Nama Usaha', 'Nama Pemilik', 'Alamat',
                'Kecamatan', 'Desa/Kelurahan',
                'Jenis Kendala', 'Kronologi',
                'Status', 'Arahan Tindak Lanjut',
                'Diverifikasi Oleh', 'Tanggal Verifikasi',
                'Tanggal Selesai',
            ], ';');

            $labelKendala = [
                'menolak_diwawancara' => 'Menolak Diwawancara',
                'tidak_ditemui'       => 'Tidak Ditemui',
                'alasan_privasi'      => 'Alasan Privasi',
                'usaha_tutup'         => 'Usaha Tutup',
                'responden_pindah'    => 'Responden Pindah',
                'tidak_ada_waktu'     => 'Tidak Ada Waktu',
                'lainnya'             => 'Lainnya',
            ];

            $labelArahan = [
                'ke_pml'            => 'Ke PML',
                'ke_taskforce'      => 'Ke Taskforce',
                'ke_subject_matter' => 'Ke Subject Matter',
            ];

            foreach ($laporan as $no => $item) {
                fputcsv($handle, [
                    $no + 1,
                    $item->nomor_tiket,
                    $item->tanggal_laporan?->format('d/m/Y H:i'),
                    $item->petugas?->name,
                    $item->petugas?->nip,
                    $item->petugas?->wilayah_tugas,
                    $item->nama_usaha,
                    $item->nama_pemilik,
                    $item->alamat_usaha,
                    $item->kecamatan,
                    $item->desa_kelurahan,
                    $labelKendala[$item->jenis_kendala] ?? $item->jenis_kendala,
                    $item->kronologi,
                    ucfirst($item->status),
                    $labelArahan[$item->arahan_tindak_lanjut] ?? '-',
                    $item->verifikator?->name ?? '-',
                    $item->tanggal_verifikasi?->format('d/m/Y H:i') ?? '-',
                    $item->tanggal_selesai?->format('d/m/Y H:i') ?? '-',
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Helper ──
    private function successResponse(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }
}