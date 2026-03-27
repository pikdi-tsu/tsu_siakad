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
        Schema::create('siakad_master_pekerjaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('id_neofeeder')->nullable()->comment('ID Mapping ke Dictionary Pekerjaan Feeder');
            $table->string('kode_pekerjaan', 5)->unique(); // Contoh: 1, 99
            $table->string('nama_pekerjaan'); // Contoh: PNS, WIRASWASTA
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_pekerjaan');
    }
};
