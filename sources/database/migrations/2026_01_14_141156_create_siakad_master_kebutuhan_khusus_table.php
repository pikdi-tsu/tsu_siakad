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
        Schema::create('siakad_master_kebutuhan_khusus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_kebutuhan', 10)->unique(); // Contoh: A, C1, D1
            $table->string('nama_kebutuhan'); // Contoh: Tuna Netra
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kebutuhan_khusus');
    }
};
