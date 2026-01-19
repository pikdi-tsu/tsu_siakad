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
        Schema::create('siakad_master_jenis_kegiatan_pendukung', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_kegiatan', 10)->unique(); // Contoh: A01, B01
            $table->string('nama_kegiatan'); // Contoh: Kuliah Kerja Nyata

            // Flag System (Opsional, tapi bagus buat nandain data bawaan)
            $table->boolean('is_system')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jenis_kegiatan_pendukung');
    }
};
