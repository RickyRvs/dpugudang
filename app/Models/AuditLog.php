<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $fillable = ['user_id','aksi','modul','referensi_id','detail','ip_address'];

    public function user() { return $this->belongsTo(User::class); }

    public static function catat(string $aksi, string $modul, $referensiId = null, $detail = null): void
    {
        self::create([
            'user_id'       => session('user_id'),
            'aksi'          => $aksi,
            'modul'         => $modul,
            'referensi_id'  => $referensiId,
            'detail'        => is_array($detail) ? json_encode($detail) : $detail,
            'ip_address'    => request()->ip(),
        ]);
    }
}