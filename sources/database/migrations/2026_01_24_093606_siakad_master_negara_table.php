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
        Schema::create('siakad_master_negara', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_negara', 3)->unique();
            $table->string('nama_negara', 150);
            $table->string('kode_emis', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_negara');
    }
};
