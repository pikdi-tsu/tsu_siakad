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
        Schema::create('siakad_master_tingkat_pendidikan_universitas', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Jenjang pendidikan sesuai PDDIKTI
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis'])->unique();

            // Dalam satuan semester
            $table->unsignedTinyInteger('masa_studi');
            $table->unsignedTinyInteger('max_cuti');
            $table->unsignedTinyInteger('max_studi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_tingkat_pendidikan_universitas');
    }
};
