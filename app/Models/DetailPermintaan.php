<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaan extends Model
{
    protected $table = 'detail_permintaan';
    protected $fillable = ['permintaan_id','barang_id','jumlah_diminta','jumlah_disetujui','jumlah_dieksekusi','satuan','keterangan'];

    public function permintaan() { return $this->belongsTo(PermintaanBarang::class, 'permintaan_id'); }
    public function barang() { return $this->belongsTo(Barang::class); }
}