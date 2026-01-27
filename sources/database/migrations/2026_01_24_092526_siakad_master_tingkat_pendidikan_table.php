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
        Schema::create('siakad_master_tingkat_pendidikan', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Kode jenjang (baku PDDIKTI)
            $table->string('jenjang', 10)->unique();

            // Nama jenjang
            $table->string('nama_jenjang_pendidikan', 100);
            $table->string('nama_jenjang_pendidikan_en', 100)->nullable();

            // Urutan untuk sorting jenjang
            $table->unsignedTinyInteger('urutan_jenjang_pendidikan');

            // Flag PDDIKTI
            $table->boolean('perguruan_tinggi');
            $table->boolean('pasca_sarjana');
            $table->boolean('jenjang_rpl');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_tingkat_pendidikan');
    }
};
