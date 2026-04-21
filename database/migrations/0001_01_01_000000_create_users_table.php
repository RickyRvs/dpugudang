<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            // role: pimpinan | manajerial | operator_gudang
            $table->enum('role', ['pimpinan', 'manajerial', 'operator_gudang']);
            $table->string('jabatan')->nullable();
            $table->string('no_hp')->nullable();
            $table->boolean('is_active')->default(true);
            // Untuk tanda tangan digital pimpinan
            $table->string('ttd_path')->nullable(); // path file gambar TTD
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};