<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\LaporanTindakLanjut;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    // Data wilayah nyata Kabupaten Blora
    private array $kecamatan = [
        ['nama' => 'Cepu',       'lat' => -7.1498, 'lng' => 111.5894],
        ['nama' => 'Blora',      'lat' => -6.9620, 'lng' => 111.4133],
        ['nama' => 'Jepon',      'lat' => -6.9780, 'lng' => 111.4800],
        ['nama' => 'Randublatung','lat'=> -7.0850, 'lng' => 111.3700],
        ['nama' => 'Kunduran',   'lat' => -6.8950, 'lng' => 111.2900],
    ];

    private array $jenisKendala = [
        'menolak_diwawancara',
        'tidak_ditemui',
        'alasan_privasi',
        'usaha_tutup',
        'responden_pindah',
        'tidak_ada_waktu',
    ];

    private array $namaUsaha = [
        'Toko Sembako Berkah', 'CV Maju Bersama', 'PT Sumber Rejeki',
        'UD Lancar Jaya', 'Warung Makan Bu Sri', 'Bengkel Pak Slamet',
        'Apotek Sehat Sejahtera', 'Toko Bahan Bangunan Karya',
        'Laundry Express Cepu', 'Minimarket Sinar Harapan',
        'Klinik Pratama Blora', 'Toko Elektronik Makmur',
        'UD Hasil Bumi Nusantara', 'CV Karya Mandiri', 'PT Blora Raya',
    ];

    private array $kronologiTemplate = [
        'Saat dikunjungi, pemilik usaha menolak untuk diwawancara dengan alasan sibuk dan tidak ingin memberikan informasi usahanya kepada pihak manapun.',
        'Petugas telah mengunjungi lokasi sebanyak 2 kali namun responden tidak berada di tempat. Tetangga menyampaikan bahwa responden sering bepergian ke luar kota.',
        'Responden bersedia ditemui namun menolak pengisian kuesioner dengan alasan khawatir data digunakan untuk keperluan pajak.',
        'Usaha ditemukan dalam kondisi tutup permanen. Berdasarkan informasi warga sekitar, usaha sudah tidak beroperasi sejak 3 bulan lalu.',
        'Responden menyampaikan bahwa tidak memiliki waktu untuk diwawancara dan meminta petugas datang kembali lain waktu tanpa memberikan jadwal pasti.',
        'Pemilik menolak dengan tegas dan meminta petugas meninggalkan lokasi. Responden menyatakan tidak ingin data pribadinya diketahui oleh siapapun.',
    ];

    public function run(): void
    {
        $petugas = User::where('role', 'petugas')
                       ->where('is_active', true)
                       ->get();

        $admin = User::where('role', 'admin')->first();

        if ($petugas->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada petugas aktif. Jalankan UserSeeder dulu.');
            return;
        }

        $this->command->info('🌱 Membuat data laporan...');

        // ── Batch 1: Laporan MENUNGGU (belum diverifikasi) ──
        $this->buatLaporan(5, 'menunggu', $petugas, null);

        // ── Batch 2: Laporan DIVERIFIKASI (menunggu tindak lanjut) ──
        $this->buatLaporan(4, 'diverifikasi', $petugas, $admin, [
            'arahan_tindak_lanjut' => 'ke_pml',
            'catatan_admin'        => 'Mohon dikunjungi ulang bersama koordinator.',
        ]);

        // ── Batch 3: Laporan DITINDAKLANJUTI ──
        $ditindaklanjuti = $this->buatLaporan(3, 'ditindaklanjuti', $petugas, $admin, [
            'arahan_tindak_lanjut' => 'ke_taskforce',
            'catatan_admin'        => 'Eskalasi ke taskforce untuk penanganan lebih lanjut.',
        ]);
        $this->tambahTindakLanjut($ditindaklanjuti, $petugas, 'akan_dikunjungi_ulang');

        // ── Batch 4: Laporan SELESAI (berhasil didata) ──
        $selesai = $this->buatLaporan(6, 'selesai', $petugas, $admin, [
            'arahan_tindak_lanjut' => 'ke_pml',
            'tanggal_selesai'      => now()->subDays(rand(1, 7)),
        ]);
        $this->tambahTindakLanjut($selesai, $petugas, 'berhasil_didata');

        // ── Batch 5: Laporan DITUTUP (tetap menolak) ──
        $ditutup = $this->buatLaporan(3, 'ditutup', $petugas, $admin, [
            'arahan_tindak_lanjut' => 'ke_subject_matter',
            'catatan_admin'        => 'Semua upaya sudah dilakukan. Kasus ditutup.',
            'tanggal_selesai'      => now()->subDays(rand(1, 5)),
        ]);
        $this->tambahTindakLanjut($ditutup, $petugas, 'tetap_menolak');

        $total = Laporan::count();
        $this->command->info("✅ LaporanSeeder selesai: {$total} laporan dibuat.");
    }

    // ─────────────────────────────────────────────
    // Helper: Buat batch laporan
    // ─────────────────────────────────────────────
    private function buatLaporan(
        int $jumlah,
        string $status,
        $petugas,
        ?User $admin,
        array $extraData = []
    ): array {
        $hasil = [];

        for ($i = 0; $i < $jumlah; $i++) {
            $petugasDipilih = $petugas->random();
            $wilayah        = $this->kecamatan[array_rand($this->kecamatan)];
            $kendala        = $this->jenisKendala[array_rand($this->jenisKendala)];
            $hariLalu       = rand(1, 30);

            // Generate nomor tiket manual untuk seeder
            $tanggal    = now()->subDays($hariLalu)->format('Ymd');
            $urutan     = str_pad(Laporan::count() + 1, 4, '0', STR_PAD_LEFT);
            $nomorTiket = "BLR-{$tanggal}-{$urutan}";

            $dataLaporan = array_merge([
                'nomor_tiket'     => $nomorTiket,
                'user_id'         => $petugasDipilih->id,
                'nama_usaha'      => $this->namaUsaha[array_rand($this->namaUsaha)],
                'nama_pemilik'    => 'Bapak/Ibu ' . $this->namaAcak(),
                'alamat_usaha'    => 'Jl. ' . $this->namaJalan() . ' No. ' . rand(1, 100),
                'kecamatan'       => $wilayah['nama'],
                'desa_kelurahan'  => 'Desa ' . $this->namaDesa(),
                'latitude'        => $wilayah['lat'] + (rand(-100, 100) / 10000),
                'longitude'       => $wilayah['lng'] + (rand(-100, 100) / 10000),
                'jenis_kendala'   => $kendala,
                'kronologi'       => $this->kronologiTemplate[array_rand($this->kronologiTemplate)],
                'status'          => $status,
                'tanggal_laporan' => now()->subDays($hariLalu)->subHours(rand(1, 8)),
            ], $extraData);

            // Tambahkan data verifikator jika ada
            if ($admin && $status !== 'menunggu') {
                $dataLaporan['diverifikasi_oleh']   = $admin->id;
                $dataLaporan['tanggal_verifikasi']  = now()->subDays($hariLalu - 1);
            }

            $laporan = Laporan::create($dataLaporan);
            $hasil[] = $laporan;
        }

        return $hasil;
    }

    // ─────────────────────────────────────────────
    // Helper: Tambah tindak lanjut ke laporan
    // ─────────────────────────────────────────────
    private function tambahTindakLanjut(array $laporan, $petugas, string $hasil): void
    {
        $keteranganMap = [
            'berhasil_didata' =>
                'Setelah kunjungan ulang dengan didampingi koordinator, responden bersedia diwawancara dan data berhasil dikumpulkan secara lengkap.',
            'tetap_menolak' =>
                'Telah dilakukan 3 kali kunjungan ulang namun responden tetap menolak. Semua upaya persuasi sudah dilakukan secara maksimal.',
            'akan_dikunjungi_ulang' =>
                'Responden bersedia ditemui namun meminta waktu tambahan. Dijadwalkan kunjungan ulang pada minggu depan.',
        ];

        foreach ($laporan as $item) {
            $petugasDipilih = $petugas->random();

            LaporanTindakLanjut::create([
                'laporan_id'        => $item->id,
                'user_id'           => $petugasDipilih->id,
                'hasil'             => $hasil,
                'keterangan'        => $keteranganMap[$hasil],
                'tanggal_kunjungan' => now()->subDays(rand(1, 3)),
            ]);
        }
    }

    // ── Generator nama acak ──
    private function namaAcak(): string
    {
        $nama = ['Slamet','Budi','Agus','Dewi','Sri','Hadi','Eko','Wati','Rini','Joko'];
        return $nama[array_rand($nama)];
    }

    private function namaJalan(): string
    {
        $jalan = ['Pemuda','Merdeka','Sudirman','Ahmad Yani','Diponegoro','Veteran','Pahlawan'];
        return $jalan[array_rand($jalan)];
    }

    private function namaDesa(): string
    {
        $desa = ['Karangboyo','Mulyorejo','Sumberpitu','Ngelo','Tambakromo','Bogorejo','Getas'];
        return $desa[array_rand($desa)];
    }
}