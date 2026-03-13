<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $fillable = ['user_id','judul','pesan','tipe','laporan_id','is_read'];

    public function user()    { return $this->belongsTo(User::class); }
    public function laporan() { return $this->belongsTo(Laporan::class); }
}