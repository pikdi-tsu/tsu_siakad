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
        Schema::create('siakad_master_golongan_pangkat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_golongan_pangkat', 10)->unique();
            $table->string('nama_golongan_pangkat', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_golongan_pangkat');
    }
};
