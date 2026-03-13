<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            //
            $table->string('nama_mitra')->nullable()->after('user_id');
            $table->string('id_mitra')->nullable()->after('nama_mitra');
            $table->string('phone_mitra')->nullable()->after('id_mitra');
            $table->string('ketua_tim')->nullable()->after('phone_mitra');
            $table->string('sumber')->default('petugas')->after('status'); // 'petugas' atau 'mitra'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropColumn(['nama_mitra', 'id_mitra', 'phone_mitra', 'ketua_tim', 'sumber']);
        });
    }
};
