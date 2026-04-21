<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock opname — pengecekan fisik stok
        Schema::create('opname', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_opname')->unique(); // OPN-2024-001
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->foreignId('dibuat_oleh')->constrained('users');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            $table->timestamp('tgl_selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_id')->constrained('opname')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang');
            $table->integer('stok_sistem'); // stok di sistem saat opname
            $table->integer('stok_fisik');  // stok aktual hasil hitung fisik
            $table->integer('selisih')->storedAs('stok_fisik - stok_sistem'); // otomatis
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_opname');
        Schema::dropIfExists('opname');
    }
};