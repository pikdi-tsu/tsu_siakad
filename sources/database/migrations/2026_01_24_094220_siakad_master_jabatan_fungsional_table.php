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
        Schema::create('siakad_master_jabatan_fungsional', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_jabatan_fungsional', 20)->unique();
            $table->string('nama_jabatan_fungsional', 150);
            $table->unsignedTinyInteger('sks_maksimal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jabatan_fungsional');
    }
};
