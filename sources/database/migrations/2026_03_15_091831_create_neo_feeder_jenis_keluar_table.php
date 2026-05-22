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
        Schema::create('neo_feeder_jenis_keluar', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('jenis_keluar');
            $table->tinyInteger('apa_mahasiswa')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neo_feeder_jenis_keluar');
    }
};
