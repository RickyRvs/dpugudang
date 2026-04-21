<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantors', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('kota');
            $table->string('alamat')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kantor_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kantor_id')->constrained('kantors')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'kantor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantor_user');
        Schema::dropIfExists('kantors');
    }
};