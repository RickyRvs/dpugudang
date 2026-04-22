<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->string('ttd_manajerial')->nullable()->after('catatan_manajerial');
            $table->string('nama_ttd_manajerial')->nullable()->after('ttd_manajerial');
            $table->datetime('tgl_ttd_manajerial')->nullable()->after('nama_ttd_manajerial');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->dropColumn(['ttd_manajerial', 'nama_ttd_manajerial', 'tgl_ttd_manajerial']);
        });
    }
};