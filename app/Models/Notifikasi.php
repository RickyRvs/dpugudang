<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $fillable = ['user_id','judul','pesan','tipe','url','permintaan_id','dibaca'];

    public function user() { return $this->belongsTo(User::class); }
    public function permintaan() { return $this->belongsTo(PermintaanBarang::class, 'permintaan_id'); }

    public static function kirim(int $userId, string $judul, string $pesan, string $tipe, string $url = null, int $permintaanId = null): void
    {
        self::create(compact('userId','judul','pesan','tipe','url','permintaanId') + ['user_id'=>$userId,'permintaan_id'=>$permintaanId]);
    }
}