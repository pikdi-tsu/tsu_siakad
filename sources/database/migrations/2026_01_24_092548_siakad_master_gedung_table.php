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
        Schema::create('siakad_master_gedung', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('kode_gedung', 20)->unique();
            $table->string('nama_gedung', 150);

            // Lokasi / kampus
            $table->string('lokasi_kampus', 150);

            // Kontak gedung
            $table->string('telepon', 30)->nullable();

            // Sarana prasarana
            $table->unsignedTinyInteger('jml_lantai');
            $table->unsignedSmallInteger('jml_ruang');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_gedung');
    }
};
