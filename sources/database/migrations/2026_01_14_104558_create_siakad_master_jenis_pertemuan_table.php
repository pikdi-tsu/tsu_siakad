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
        Schema::create('siakad_master_jenis_pertemuan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_jenis', 10)->unique(); // Contoh: K, P, T
            $table->string('nama_jenis'); // Contoh: Kuliah, Praktikum
            $table->string('nama_singkat')->nullable(); // Contoh: Kuliah, Prak

            // Kolom Logika Boolean
            $table->boolean('is_hitung_presensi')->default(true); // Masuk Persentase Presensi?
            $table->boolean('is_ujian')->default(false); // Termasuk Ujian?

            // Kolom Kategori (Kolom 'Jenis' di screenshot)
            $table->string('kelompok_jenis')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jenis_pertemuan');
    }
};
