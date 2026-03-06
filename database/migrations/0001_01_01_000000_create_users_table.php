<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password'); // bcrypt hash
            $table->enum('role', [
                'petugas',        // PPL/PML - input laporan
                'koordinator',    // Koordinator Lapangan
                'admin',          // Admin Kabupaten
                'pimpinan'        // Kepala BPS
            ])->default('petugas');
            $table->string('nip')->nullable()->comment('Nomor Induk Pegawai');
            $table->string('phone')->nullable();
            $table->string('wilayah_tugas')->nullable()->comment('Kecamatan/Desa tugas');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // untuk audit trail
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};