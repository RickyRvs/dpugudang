<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';
    protected $fillable = ['nama','kode','deskripsi'];

    public function barang() { return $this->hasMany(Barang::class, 'kategori_id'); }
}