<?php

namespace App\Services;

use App\Models\Laporan;
use Illuminate\Support\Facades\DB;

class NomorTiketService
{
    /**
     * Generate nomor tiket unik
     * Format: BLR-YYYYMMDD-0001
     * Contoh: BLR-20250115-0042
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            $tanggal = now()->format('Ymd');
            $prefix  = "BLR-{$tanggal}-";

            // Hitung laporan hari ini (dengan lock untuk hindari race condition)
            $jumlahHariIni = Laporan::where('nomor_tiket', 'like', $prefix . '%')
                                    ->lockForUpdate()
                                    ->count();

            $urutan      = $jumlahHariIni + 1;
            $nomorTiket  = $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);

            // Pastikan benar-benar unik (failsafe)
            while (Laporan::where('nomor_tiket', $nomorTiket)->exists()) {
                $urutan++;
                $nomorTiket = $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
            }

            return $nomorTiket;
        });
    }
}