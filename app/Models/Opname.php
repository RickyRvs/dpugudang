<?php
// ─────────────────────────────────────────────────────────
// app/Models/Opname.php
// ─────────────────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Opname extends Model
{
    protected $table = 'opname';
    protected $fillable = [
        'kantor_id', 'nomor_opname', 'tanggal', 'keterangan',
        'status', 'dibuat_oleh', 'disetujui_oleh', 'tgl_selesai',
    ];
    protected $casts = ['tgl_selesai' => 'datetime'];

    public function kantor()    { return $this->belongsTo(Kantor::class); }
    public function pembuat()   { return $this->belongsTo(User::class, 'dibuat_oleh'); }
    public function penyetuju() { return $this->belongsTo(User::class, 'disetujui_oleh'); }
    public function details()   { return $this->hasMany(DetailOpname::class); }

    public function scopeForKantor(Builder $query, ?int $kantorId = null): Builder
    {
        $id = $kantorId ?? session('kantor_id');
        if ($id) $query->where('kantor_id', $id);
        return $query;
    }

    public static function generateNomor(): string
    {
        $year = date('Y');
        $last = self::where('nomor_opname', 'like', "OPN-{$year}-%")->count();
        return sprintf('OPN-%s-%03d', $year, $last + 1);
    }
}