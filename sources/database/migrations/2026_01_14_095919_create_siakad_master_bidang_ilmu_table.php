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
        Schema::create('siakad_master_bidang_ilmu', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_bidang_ilmu', 20)->unique(); // Contoh: 111, 123
            $table->string('nama_bidang_ilmu'); // Contoh: Fisika, Ilmu Komputer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_bidang_ilmu');
    }
};
