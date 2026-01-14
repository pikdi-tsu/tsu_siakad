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
        Schema::create('siakad_master_agama', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_agama', 5)->unique(); // Contoh: 1, 2, 99
            $table->string('nama_agama'); // Contoh: Islam, Kristen Protestan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_agama');
    }
};
