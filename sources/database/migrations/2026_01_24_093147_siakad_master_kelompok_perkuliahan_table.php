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
        Schema::create('siakad_master_kelompok_perkuliahan', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('nama_kelompok_perkuliahan', 100);
            $table->unsignedTinyInteger('urutan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kelompok_perkuliahan');
    }
};
