<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // ── Pimpinan ──
            [
                'name'          => 'Kepala BPS Blora',
                'username'      => 'kepala_bps',
                'email'         => 'kepala@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'pimpinan',
                'nip'           => '196801011990031001',
                'phone'         => '081234560001',
                'wilayah_tugas' => 'Kabupaten Blora',
                'is_active'     => true,
            ],

            // ── Admin ──
            [
                'name'          => 'Admin Kabupaten Blora',
                'username'      => 'admin_blora',
                'email'         => 'admin@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'admin',
                'nip'           => '199001012015041001',
                'phone'         => '081234560002',
                'wilayah_tugas' => 'Kabupaten Blora',
                'is_active'     => true,
            ],

            // ── Koordinator ──
            [
                'name'          => 'Koordinator Cepu',
                'username'      => 'koord_cepu',
                'email'         => 'koord.cepu@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'koordinator',
                'nip'           => '199203152018021001',
                'phone'         => '081234560003',
                'wilayah_tugas' => 'Kecamatan Cepu',
                'is_active'     => true,
            ],
            [
                'name'          => 'Koordinator Blora',
                'username'      => 'koord_blora',
                'email'         => 'koord.blora@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'koordinator',
                'nip'           => '199105202018021002',
                'phone'         => '081234560004',
                'wilayah_tugas' => 'Kecamatan Blora',
                'is_active'     => true,
            ],

            // ── Petugas / PPL ──
            [
                'name'          => 'Budi Santoso',
                'username'      => 'budi_ppl01',
                'email'         => 'budi@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'petugas',
                'nip'           => '199501012020011001',
                'phone'         => '081234560005',
                'wilayah_tugas' => 'Kecamatan Cepu',
                'is_active'     => true,
            ],
            [
                'name'          => 'Siti Rahayu',
                'username'      => 'siti_ppl02',
                'email'         => 'siti@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'petugas',
                'nip'           => '199702282020012002',
                'phone'         => '081234560006',
                'wilayah_tugas' => 'Kecamatan Cepu',
                'is_active'     => true,
            ],
            [
                'name'          => 'Agus Prayitno',
                'username'      => 'agus_ppl03',
                'email'         => 'agus@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'petugas',
                'nip'           => '199803102020011003',
                'phone'         => '081234560007',
                'wilayah_tugas' => 'Kecamatan Blora',
                'is_active'     => true,
            ],
            [
                'name'          => 'Dewi Lestari',
                'username'      => 'dewi_ppl04',
                'email'         => 'dewi@bpsblora.go.id',
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'petugas',
                'nip'           => '200001152020012004',
                'phone'         => '081234560008',
                'wilayah_tugas' => 'Kecamatan Jepon',
                'is_active'     => true,
            ],
            // Akun nonaktif untuk test blokir
            [
                'name'          => 'Petugas Nonaktif',
                'username'      => 'nonaktif_ppl',
                'email'         => null,
                'password'      => Hash::make('Admin@1234'),
                'role'          => 'petugas',
                'nip'           => null,
                'phone'         => null,
                'wilayah_tugas' => 'Kecamatan Blora',
                'is_active'     => false, // ← sengaja dinonaktifkan
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                $userData
            );
        }

        $this->command->info('✅ UserSeeder selesai: ' . count($users) . ' user dibuat.');
    }
}