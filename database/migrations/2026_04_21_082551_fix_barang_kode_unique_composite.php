<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Hapus unique constraint lama pada kode saja
            $table->dropUnique('barang_kode_unique');

            // Tambah composite unique: kode + kantor_id
            // (kode boleh sama asal beda kantor)
            $table->unique(['kode', 'kantor_id'], 'barang_kode_kantor_unique');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropUnique('barang_kode_kantor_unique');
            $table->unique('kode', 'barang_kode_unique');
        });
    }
};