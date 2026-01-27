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
        Schema::create('siakad_master_konsentrasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_studi_id');

            $table->string('kode', 20);
            $table->string('nama_konsentrasi', 200);
            $table->string('nama_konsentrasi_en', 200)->nullable();

            $table->timestamps();

            $table->unique(['program_studi_id', 'kode']);

            $table->foreign('program_studi_id')
                ->references('id')
                ->on('siakad_master_program_studi')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_konsentrasi');
    }
};
