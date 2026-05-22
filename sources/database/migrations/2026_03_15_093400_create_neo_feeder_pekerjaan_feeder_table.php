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
        Schema::create('neo_feeder_pekerjaan_feeder', function (Blueprint $table) {
            $table->integer('id_pekerjaan')->primary();
            $table->string('nama_pekerjaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neo_feeder_pekerjaan_feeder');
    }
};
