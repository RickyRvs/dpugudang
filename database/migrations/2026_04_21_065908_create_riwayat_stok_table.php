<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaan_barang')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // siapa yang input
            $table->enum('tipe', ['masuk', 'keluar', 'koreksi', 'opname']); // tipe transaksi
            $table->integer('jumlah'); // selalu positif
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('referensi')->nullable(); // nomor permintaan atau referensi lain
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
    }
};