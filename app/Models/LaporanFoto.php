<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LaporanFoto extends Model
{
    protected $table = 'laporan_foto';

    protected $fillable = [
        'laporan_id', 'path_foto', 'nama_file_asli',
        'latitude', 'longitude', 'urutan',
    ];

    protected $appends = ['url_foto'];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    // Otomatis generate URL publik saat response
    public function getUrlFotoAttribute(): string
    {
        return Storage::url($this->path_foto);
    }
}