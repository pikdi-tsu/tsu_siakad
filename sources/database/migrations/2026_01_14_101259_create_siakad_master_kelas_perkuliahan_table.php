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
        Schema::create('siakad_master_kelas_perkuliahan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_kelas', 10)->unique(); // Contoh: A, B, C
            $table->string('nama_kelas'); // Contoh: Kelas A
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kelas_perkuliahan');
    }
};
