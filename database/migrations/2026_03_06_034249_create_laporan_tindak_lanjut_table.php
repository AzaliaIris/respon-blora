<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')
                  ->constrained('laporan')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Siapa yang melakukan tindak lanjut');

            $table->enum('hasil', [
                'berhasil_didata',
                'tetap_menolak',
                'akan_dikunjungi_ulang',
            ]);
            $table->text('keterangan')->comment('Detail hasil kunjungan ulang');
            $table->timestamp('tanggal_kunjungan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tindak_lanjut');
    }
};