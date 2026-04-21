<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // BRG-001
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategori_barang')->onDelete('restrict');
            $table->string('satuan'); // pcs, kg, liter, box, dll
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            // Stok
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_minimum')->default(0); // batas minimum untuk alert
            $table->string('lokasi_rak')->nullable(); // misal: Rak A-1
            // Harga
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};