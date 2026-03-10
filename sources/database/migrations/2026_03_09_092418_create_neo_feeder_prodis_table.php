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
        Schema::create('neo_feeder_prodis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('id_prodi', 36)->unique(); // UUID dari Feeder
            $table->string('kode_program_studi', 50)->nullable();
            $table->string('nama_program_studi');
            $table->char('status', 5)->nullable(); // 'A' = Aktif
            $table->string('id_jenjang_pendidikan', 10)->nullable();
            $table->string('nama_jenjang_pendidikan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neo_feeder_prodis');
    }
};
