<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'nama_usaha',
        'nama_pemilik',
        'alamat_usaha',
        'kecamatan',
        'desa_kelurahan',
        'latitude',
        'longitude',
        'jenis_kendala',
        'jenis_kendala_lainnya',
        'kronologi',
        'status',
        'arahan_tindak_lanjut',
        'catatan_admin',
        'tanggal_laporan',
        'tanggal_verifikasi',
        'tanggal_selesai',
        'diverifikasi_oleh',
    ];

    protected $casts = [
        'latitude'            => 'float',
        'longitude'           => 'float',
        'tanggal_laporan'     => 'datetime',
        'tanggal_verifikasi'  => 'datetime',
        'tanggal_selesai'     => 'datetime',
    ];

    // ── Relasi ──
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function foto(): HasMany
    {
        return $this->hasMany(LaporanFoto::class);
    }

    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(LaporanTindakLanjut::class);
    }

    // ── Scope filter status ──
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Label jenis kendala (untuk tampilan) ──
    public function getJenisKendalaLabelAttribute(): string
    {
        return match($this->jenis_kendala) {
            'menolak_diwawancara' => 'Menolak Diwawancara',
            'tidak_ditemui'       => 'Tidak Ditemui',
            'alasan_privasi'      => 'Alasan Privasi',
            'usaha_tutup'         => 'Usaha Tutup',
            'responden_pindah'    => 'Responden Pindah',
            'tidak_ada_waktu'     => 'Tidak Ada Waktu',
            'lainnya'             => 'Lainnya: ' . $this->jenis_kendala_lainnya,
            default               => $this->jenis_kendala,
        };
    }
}