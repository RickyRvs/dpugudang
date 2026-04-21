<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FLOW:
     * 1. Manajerial buat permintaan (status: draft → diajukan)
     * 2. Pimpinan review → acc/tolak (status: disetujui | ditolak)
     *    - Jika disetujui: pimpinan upload TTD / generate TTD digital
     * 3. Manajerial konfirmasi & kirim ke operator (status: dikirim_operator)
     * 4. Operator eksekusi stok (status: selesai) → stok +/- otomatis
     */
    public function up(): void
    {
        Schema::create('permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permintaan')->unique(); // PRB-2024-0001
            // Jenis: masuk (barang masuk ke gudang) | keluar (barang keluar dari gudang)
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->text('keperluan'); // alasan/tujuan permintaan
            $table->string('departemen_tujuan')->nullable(); // kalau keluar, tujuannya
            $table->date('tanggal_dibutuhkan');

            // Status alur approval
            $table->enum('status', [
                'draft',            // baru dibuat, belum diajukan
                'diajukan',         // sudah diajukan ke pimpinan
                'disetujui',        // sudah di-acc pimpinan
                'ditolak',          // ditolak pimpinan
                'dikirim_operator', // manajerial sudah forward ke operator
                'diproses',         // operator sedang proses
                'selesai',          // stok sudah diupdate
                'dibatalkan',       // dibatalkan manajerial sebelum diproses
            ])->default('draft');

            // Relasi user
            $table->foreignId('dibuat_oleh')->constrained('users')->onDelete('restrict'); // manajerial
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null'); // pimpinan
            $table->foreignId('dieksekusi_oleh')->nullable()->constrained('users')->onDelete('set null'); // operator

            // Approval pimpinan
            $table->timestamp('tgl_diajukan')->nullable();
            $table->timestamp('tgl_disetujui')->nullable();
            $table->text('catatan_pimpinan')->nullable(); // catatan acc/tolak
            $table->string('ttd_pimpinan')->nullable(); // path file TTD
            $table->string('nama_ttd_pimpinan')->nullable(); // nama pimpinan yang TTD

            // Operator
            $table->timestamp('tgl_dikirim_operator')->nullable();
            $table->timestamp('tgl_selesai')->nullable();
            $table->text('catatan_operator')->nullable();

            // Catatan umum
            $table->text('catatan_manajerial')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang');
    }
};