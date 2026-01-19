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
        Schema::create('siakad_master_periode_akademik', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_periode', 10)->unique(); // Contoh: 20251
            $table->string('nama_periode'); // Contoh: 2025 Ganjil

            // Tanggal Penting
            $table->date('tgl_awal_kuliah')->nullable();
            $table->date('tgl_akhir_kuliah')->nullable();
            $table->date('tgl_awal_uts')->nullable();
            $table->date('tgl_akhir_uts')->nullable(); // Tambahan biar lengkap
            $table->date('tgl_awal_uas')->nullable();
            $table->date('tgl_akhir_uas')->nullable(); // Tambahan biar lengkap

            // Status Aktif (Hanya 1 yang boleh true)
            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_periode_akademik');
    }
};
