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
        Schema::create('siakad_master_unsur_nilai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_unsur', 20)->unique(); // Contoh: 1, PETA1
            $table->string('nama_unsur'); // Contoh: TUGAS, PENGUJI TA
            $table->string('nama_singkat')->nullable(); // Contoh: TGINV
            $table->string('kelompok_unsur')->nullable(); // Bisa jadi kategori
            $table->text('metode_evaluasi')->nullable(); // Deskripsi panjang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_unsur_nilai');
    }
};
