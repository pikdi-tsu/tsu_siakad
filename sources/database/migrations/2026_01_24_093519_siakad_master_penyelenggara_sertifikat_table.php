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
        Schema::create('siakad_master_penyelenggara_sertifikat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_penyelenggara_sertifikat', 200)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_penyelenggara_sertifikat');
    }
};
