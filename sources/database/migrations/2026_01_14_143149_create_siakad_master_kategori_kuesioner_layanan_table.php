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
        Schema::create('siakad_master_kategori_kuesioner_layanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kategori'); // Contoh: Sub Bagian Akademik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kategori_kuesioner_layanan');
    }
};
