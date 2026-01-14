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
        Schema::create('siakad_master_kelompok_matakuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_kelompok', 10)->unique(); // Contoh: MBB, MPK
            $table->string('nama_kelompok'); // Contoh: Mata Kuliah Berkehidupan Bermasyarakat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kelompok_matakuliah');
    }
};
