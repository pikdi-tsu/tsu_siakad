<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siakad_master_jenis_modul_mata_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_modul', 10)->unique(); // Contoh: P, T, TT
            $table->string('nama_modul'); // Contoh: Praktikum, Teori
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jenis_modul_mata_kuliah');
    }
};
