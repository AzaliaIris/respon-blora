<?php
namespace App\Services;
use App\Models\{Notifikasi, User, Laporan};

class NotifikasiService
{
    public static function laporanBaru(Laporan $laporan): void
    {
        $penerima = collect();

        // Admin & pimpinan — semua dapat
        $penerima = $penerima->merge(
            User::whereIn('role', ['admin','pimpinan'])->where('is_active', true)->pluck('id')
        );

        // Koordinator — yang wilayahnya cocok
        $kec = preg_replace('/^kecamatan\s+/i', '', trim($laporan->kecamatan));
        User::where('role','koordinator')->where('is_active', true)->get()
            ->each(function($k) use (&$penerima, $kec) {
                $w = preg_replace('/^kecamatan\s+/i', '', trim($k->wilayah_tugas));
                if (stripos($w, $kec) !== false || stripos($kec, $w) !== false) {
                    $penerima->push($k->id);
                }
            });

        foreach ($penerima->unique() as $uid) {
            Notifikasi::create([
                'user_id'    => $uid,
                'judul'      => 'Laporan Baru Masuk',
                'pesan'      => "Laporan #{$laporan->nomor_tiket} dari {$laporan->nama_usaha} ({$laporan->kecamatan})",
                'tipe'       => 'laporan_baru',
                'laporan_id' => $laporan->id,
            ]);
        }
    }

    public static function statusBerubah(Laporan $laporan, string $statusBaru): void
    {
        $cfg = [
            'diverifikasi'    => ['judul'=>'Laporan Diverifikasi',    'tipe'=>'diverifikasi'],
            'ditindaklanjuti' => ['judul'=>'Laporan Ditindaklanjuti', 'tipe'=>'ditindaklanjuti'],
            'selesai'         => ['judul'=>'Laporan Selesai',         'tipe'=>'selesai'],
            'ditutup'         => ['judul'=>'Laporan Ditutup',         'tipe'=>'ditutup'],
        ];
        if (!isset($cfg[$statusBaru])) return;

        $c = $cfg[$statusBaru];

        // Notif ke petugas pembuat laporan
        Notifikasi::create([
            'user_id'    => $laporan->user_id,
            'judul'      => $c['judul'],
            'pesan'      => "Laporan #{$laporan->nomor_tiket} ({$laporan->nama_usaha}) telah {$statusBaru}.",
            'tipe'       => $c['tipe'],
            'laporan_id' => $laporan->id,
        ]);

        // Notif ke admin & pimpinan juga
        User::whereIn('role',['admin','pimpinan'])->where('is_active',true)
            ->where('id','!=',$laporan->user_id)
            ->pluck('id')
            ->each(fn($uid) => Notifikasi::create([
                'user_id'    => $uid,
                'judul'      => $c['judul'],
                'pesan'      => "Laporan #{$laporan->nomor_tiket} ({$laporan->nama_usaha}) telah {$statusBaru}.",
                'tipe'       => $c['tipe'],
                'laporan_id' => $laporan->id,
            ]));

        // Notif ke petugas yang ditugaskan (PML/Subject Matter)
        if ($laporan->ditugaskan_ke && $laporan->ditugaskan_ke !== $laporan->user_id && $statusBaru === 'diverifikasi') {
            Notifikasi::create([
                'user_id'    => $laporan->ditugaskan_ke,
                'judul'      => 'Anda Mendapat Tugas Tindak Lanjut',
                'pesan'      => "Laporan #{$laporan->nomor_tiket} ({$laporan->nama_usaha}) ditugaskan kepada Anda untuk ditindaklanjuti.",
                'tipe'       => 'tugas',
                'laporan_id' => $laporan->id,
            ]);
        }

        // Notif ke semua Taskforce jika arahan ke_taskforce
        if ($laporan->arahan_tindak_lanjut === 'ke_taskforce' && $statusBaru === 'diverifikasi') {
            User::where('posisi', 'taskforce')
                ->where('is_active', true)
                ->pluck('id')
                ->each(fn($uid) => Notifikasi::create([
                    'user_id'    => $uid,
                    'judul'      => 'Tugas Tindak Lanjut Taskforce',
                    'pesan'      => "Laporan #{$laporan->nomor_tiket} ({$laporan->nama_usaha}) memerlukan tindak lanjut dari tim Taskforce.",
                    'tipe'       => 'tugas',
                    'laporan_id' => $laporan->id,
                ]));
        }
    }
}