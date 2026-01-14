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
        Schema::create('siakad_master_status_hadir', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_status', 10)->unique(); // H, A, I, S
            $table->string('nama_status'); // Hadir, Alfa

            // 3 Kolom Logika
            $table->boolean('is_hitung_hadir')->default(false); // Apakah mengurangi jatah bolos?
            $table->boolean('is_untuk_dosen')->default(true);
            $table->boolean('is_untuk_mahasiswa')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_status_hadir');
    }
};
