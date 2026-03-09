<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanTindakLanjut extends Model
{
    protected $table = 'laporan_tindak_lanjut';

    protected $fillable = [
        'laporan_id', 'user_id', 'hasil',
        'keterangan', 'tanggal_kunjungan',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}