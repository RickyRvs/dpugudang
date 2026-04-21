<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Kantor extends Model
{
    protected $table = 'kantors';
    protected $fillable = ['kode', 'nama', 'kota', 'alamat', 'is_active'];
 
    public function users()
    {
        return $this->belongsToMany(User::class, 'kantor_user');
    }
 
    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
 
    public function permintaanBarang()
    {
        return $this->hasMany(PermintaanBarang::class);
    }
 
    public function opname()
    {
        return $this->hasMany(Opname::class);
    }
 
    public function riwayatStok()
    {
        return $this->hasMany(RiwayatStok::class);
    }
}