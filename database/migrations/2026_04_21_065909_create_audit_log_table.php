<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('aksi'); // login, logout, buat_permintaan, setujui, tolak, dll
            $table->string('modul'); // permintaan, barang, stok, user, dll
            $table->string('referensi_id')->nullable(); // ID record yang diubah
            $table->text('detail')->nullable(); // JSON atau teks deskripsi
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};