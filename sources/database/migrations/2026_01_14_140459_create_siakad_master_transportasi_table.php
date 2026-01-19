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
        Schema::create('siakad_master_transportasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_transportasi', 5)->unique(); // Contoh: 0, 1
            $table->string('nama_transportasi'); // Contoh: Motor, Mobil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_transportasi');
    }
};
