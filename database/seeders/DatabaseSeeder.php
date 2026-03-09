<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('🗑️  Membersihkan tabel lama...');
        DB::table('laporan_tindak_lanjut')->truncate();
        DB::table('laporan_foto')->truncate();
        DB::table('laporan')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('==============================');
        $this->command->info('  RESPON BLORA — DB SEEDER   ');
        $this->command->info('==============================');

        $this->call([
            UserSeeder::class,
            LaporanSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Semua seeder selesai!');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Username', 'Password'],
            [
                ['Pimpinan',     'kepala_bps',   'Admin@1234'],
                ['Admin',        'admin_blora',  'Admin@1234'],
                ['Koordinator',  'koord_cepu',   'Admin@1234'],
                ['Koordinator',  'koord_blora',  'Admin@1234'],
                ['Petugas',      'budi_ppl01',   'Admin@1234'],
                ['Petugas',      'siti_ppl02',   'Admin@1234'],
                ['Petugas',      'agus_ppl03',   'Admin@1234'],
                ['Petugas',      'dewi_ppl04',   'Admin@1234'],
                ['Nonaktif ❌',  'nonaktif_ppl', 'Admin@1234'],
            ]
        );
    }
}