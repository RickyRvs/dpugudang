<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // penerima
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe'); // permintaan_baru, disetujui, ditolak, dikirim_operator, selesai
            $table->string('url')->nullable(); // link langsung ke halaman terkait
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaan_barang')->onDelete('cascade');
            $table->boolean('dibaca')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};