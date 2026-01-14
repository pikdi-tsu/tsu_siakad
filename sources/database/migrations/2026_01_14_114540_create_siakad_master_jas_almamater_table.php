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
        Schema::create('siakad_master_jas_almamater', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_ukuran', 10)->unique(); // Contoh: S, M, XL, XXXL
            $table->string('nama_ukuran'); // Contoh: Small, Medium, atau sama dengan kode
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jas_almamater');
    }
};
