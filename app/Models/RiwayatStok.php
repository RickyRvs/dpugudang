<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';
    protected $fillable = [
        'kantor_id', 'barang_id', 'permintaan_id', 'user_id',
        'tipe', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'referensi', 'keterangan',
    ];

    public function kantor()    { return $this->belongsTo(Kantor::class); }
    public function barang()    { return $this->belongsTo(Barang::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function permintaan(){ return $this->belongsTo(PermintaanBarang::class, 'permintaan_id'); }

    public function scopeForKantor(Builder $query, ?int $kantorId = null): Builder
    {
        $id = $kantorId ?? session('kantor_id');
        if ($id) $query->where('kantor_id', $id);
        return $query;
    }
}