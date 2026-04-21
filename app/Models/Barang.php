<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = [
        'kantor_id', 'kode', 'nama', 'kategori_id', 'satuan', 'deskripsi',
        'foto', 'stok_tersedia', 'stok_minimum', 'lokasi_rak', 'harga_satuan', 'is_active'
    ];

    // ── Relationships ─────────────────────────────────
    public function kantor()          { return $this->belongsTo(Kantor::class); }
    public function kategori()        { return $this->belongsTo(KategoriBarang::class, 'kategori_id'); }
    public function riwayatStok()     { return $this->hasMany(RiwayatStok::class); }
    public function detailPermintaan(){ return $this->hasMany(DetailPermintaan::class); }

    // ── Scope: filter by kantor aktif dari session ────
    // Dipakai di semua Controller: Barang::forKantor()->...
    public function scopeForKantor(Builder $query, ?int $kantorId = null): Builder
    {
        $id = $kantorId ?? session('kantor_id');
        if ($id) {
            $query->where('kantor_id', $id);
        }
        return $query;
    }
}