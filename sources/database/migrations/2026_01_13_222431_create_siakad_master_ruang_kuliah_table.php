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
        Schema::create('siakad_master_ruang_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Pakai UUID
            $table->string('kode_ruang', 50)->unique(); // Kode biasanya unik (misal: B13)
            $table->string('nama_ruang'); // Gabungan, Ruang B 1.3
            $table->string('unit')->nullable(); // Universitas Tiga Serangkai (Bisa diganti FK ke tabel unit/fakultas nanti)
            $table->string('lokasi')->nullable(); // Gedung B Lantai 1
            $table->integer('kapasitas')->default(0);
            $table->boolean('is_active')->default(true); // 1 = Aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_ruang_kuliah');
    }
};
