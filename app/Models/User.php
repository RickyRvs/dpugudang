<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['nama', 'email', 'password', 'role', 'jabatan', 'no_hp', 'is_active', 'ttd_path'];
    protected $hidden = ['password'];

    public function permintaanDibuat()   { return $this->hasMany(PermintaanBarang::class, 'dibuat_oleh'); }
    public function permintaanDisetujui(){ return $this->hasMany(PermintaanBarang::class, 'disetujui_oleh'); }
    public function notifikasi()         { return $this->hasMany(Notifikasi::class); }
    public function auditLogs()          { return $this->hasMany(AuditLog::class); }
    public function kantors()            { return $this->belongsToMany(Kantor::class, 'kantor_user'); }
}