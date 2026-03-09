<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();

            // ── Nomor Tiket Otomatis ──
            $table->string('nomor_tiket', 30)->unique()
                  ->comment('Format: BLR-YYYYMMDD-XXXX');

            // ── Identitas Pelapor ──
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Petugas yang melapor');

            // ── Data Responden/Usaha ──
            $table->string('nama_usaha')->comment('Nama usaha/perusahaan/responden');
            $table->string('nama_pemilik')->nullable()->comment('Nama pemilik/penanggung jawab');
            $table->text('alamat_usaha')->comment('Alamat lengkap usaha');
            $table->string('kecamatan', 100);
            $table->string('desa_kelurahan', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat GPS');
            $table->decimal('longitude', 11, 8)->nullable();

            // ── Jenis Kendala ──
            $table->enum('jenis_kendala', [
                'menolak_diwawancara',
                'tidak_ditemui',
                'alasan_privasi',
                'usaha_tutup',
                'responden_pindah',
                'tidak_ada_waktu',
                'lainnya',
            ]);
            $table->string('jenis_kendala_lainnya')->nullable()
                  ->comment('Diisi jika jenis_kendala = lainnya');

            // ── Kronologi ──
            $table->text('kronologi')->comment('Uraian singkat kejadian di lapangan');

            // ── Status Laporan ──
            $table->enum('status', [
                'menunggu',       // baru dikirim, belum diverifikasi
                'diverifikasi',   // sudah dicek admin
                'ditindaklanjuti',// sedang proses tindak lanjut
                'selesai',        // berhasil didata
                'ditutup',        // tetap menolak, kasus ditutup
            ])->default('menunggu');

            // ── Arahan Tindak Lanjut (diisi Admin) ──
            $table->enum('arahan_tindak_lanjut', [
                'ke_pml',
                'ke_taskforce',
                'ke_subject_matter',
            ])->nullable();

            $table->text('catatan_admin')->nullable()
                  ->comment('Catatan verifikasi dari admin');

            // ── Tracking Waktu ──
            $table->timestamp('tanggal_laporan')->useCurrent();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();

            // ── Relasi Admin yang Memverifikasi ──
            $table->foreignId('diverifikasi_oleh')
                  ->nullable()
                  ->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // ── Index untuk performa query ──
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['kecamatan', 'status']);
            $table->index('nomor_tiket');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};