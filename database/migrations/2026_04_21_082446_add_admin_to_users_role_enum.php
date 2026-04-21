<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('operator_gudang','manajerial','pimpinan','admin') NOT NULL DEFAULT 'operator_gudang'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('operator_gudang','manajerial','pimpinan') NOT NULL DEFAULT 'operator_gudang'");
    }
};