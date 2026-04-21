<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Barang jadi per-kantor
        Schema::table('barang', function (Blueprint $table) {
            $table->unsignedBigInteger('kantor_id')->nullable()->after('id');
            $table->foreign('kantor_id')->references('id')->on('kantors')->nullOnDelete();
        });

        // Permintaan barang per-kantor (kantor yang mengajukan)
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('kantor_id')->nullable()->after('id');
            $table->foreign('kantor_id')->references('id')->on('kantors')->nullOnDelete();
        });

        // Opname per-kantor
        Schema::table('opname', function (Blueprint $table) {
            $table->unsignedBigInteger('kantor_id')->nullable()->after('id');
            $table->foreign('kantor_id')->references('id')->on('kantors')->nullOnDelete();
        });

        // Riwayat stok ikut kantor barangnya — simpan juga kantor_id biar query laporan gampang
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->unsignedBigInteger('kantor_id')->nullable()->after('id');
            $table->foreign('kantor_id')->references('id')->on('kantors')->nullOnDelete();
        });

        // Tambah role admin ke users (enum alter, lebih aman pakai string check)
        // Kalau pakai enum: ALTER TABLE users MODIFY COLUMN role ENUM(...)
        // Pakai string aja biar fleksibel — sesuaikan jika existing pakai enum
        // Schema::table('users', function (Blueprint $table) {
        //     $table->string('role')->change(); // jika perlu ubah enum ke string
        // });
    }

    public function down(): void
    {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn('kantor_id');
        });
        Schema::table('opname', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn('kantor_id');
        });
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn('kantor_id');
        });
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['kantor_id']);
            $table->dropColumn('kantor_id');
        });
    }
};