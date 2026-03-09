<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')
                  ->constrained('laporan')
                  ->onDelete('cascade');
            $table->string('path_foto')->comment('Path file foto di storage');
            $table->string('nama_file_asli')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->comment('GPS dari EXIF foto');
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_foto');
    }
};