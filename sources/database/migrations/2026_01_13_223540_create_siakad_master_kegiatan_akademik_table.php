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
        Schema::create('siakad_master_kegiatan_akademik', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_kegiatan', 20)->unique(); // Contoh: 01, 02
            $table->string('nama_kegiatan'); // Contoh: KKN, Wisuda
            $table->string('warna_background', 7)->nullable(); // Format Hex: #RRGGBB
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kegiatan_akademik');
    }
};
