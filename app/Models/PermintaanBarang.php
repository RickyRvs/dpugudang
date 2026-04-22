<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';
    protected $fillable = [
        'kantor_id',
        'nomor_permintaan', 'jenis', 'keperluan', 'departemen_tujuan', 'tanggal_dibutuhkan',
        'status', 'dibuat_oleh', 'disetujui_oleh', 'dieksekusi_oleh',
        'tgl_diajukan', 'tgl_disetujui', 'catatan_pimpinan', 'ttd_pimpinan', 'nama_ttd_pimpinan',
        'tgl_dikirim_operator', 'tgl_selesai', 'catatan_operator', 'catatan_manajerial',
        // ── TTD Manajerial (wajib sebelum diajukan) ──
        'ttd_manajerial', 'nama_ttd_manajerial', 'tgl_ttd_manajerial',
    ];
    protected $casts = [
        'tgl_diajukan'         => 'datetime',
        'tgl_disetujui'        => 'datetime',
        'tgl_dikirim_operator' => 'datetime',
        'tgl_selesai'          => 'datetime',
        'tgl_ttd_manajerial'   => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────
    public function kantor()     { return $this->belongsTo(Kantor::class); }
    public function pembuat()    { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penyetuju()  { return $this->belongsTo(User::class, 'disetujui_oleh'); }
    public function pelaksana()  { return $this->belongsTo(User::class, 'dieksekusi_oleh'); }
    public function details()    { return $this->hasMany(DetailPermintaan::class, 'permintaan_id'); }
    public function notifikasi() { return $this->hasMany(Notifikasi::class, 'permintaan_id'); }

    // ── Scope: filter by kantor ───────────────────────
    public function scopeForKantor(Builder $query, ?int $kantorId = null): Builder
    {
        $id = $kantorId ?? session('kantor_id');
        if ($id) {
            $query->where('kantor_id', $id);
        }
        return $query;
    }

    // ── Generate nomor permintaan ─────────────────────
    public static function generateNomor(string $jenis): string
    {
        $prefix = $jenis === 'masuk' ? 'PBM' : 'PBK';
        $year   = date('Y');
        $last   = self::where('nomor_permintaan', 'like', "{$prefix}-{$year}-%")->count();
        return sprintf('%s-%s-%04d', $prefix, $year, $last + 1);
    }
}