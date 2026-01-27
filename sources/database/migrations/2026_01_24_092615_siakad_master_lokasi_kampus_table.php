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
        Schema::create('siakad_master_lokasi_kampus', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->text('alamat');
            $table->string('telepon', 30)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_lokasi_kampus');
    }
};
