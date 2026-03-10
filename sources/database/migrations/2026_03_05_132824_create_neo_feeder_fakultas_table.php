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
        Schema::create('neo_feeder_fakultas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('id_fakultas', 36)->unique();
            $table->string('nama_fakultas');
            $table->char('status', 5)->nullable();
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
        Schema::dropIfExists('neo_feeder_fakultas');
    }
};
