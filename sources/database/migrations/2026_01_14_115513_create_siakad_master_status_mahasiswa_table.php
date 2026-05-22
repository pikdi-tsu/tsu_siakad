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
        Schema::create('siakad_master_status_mahasiswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('id_neofeeder')->nullable()->comment('ID Mapping ke Dictionary Jenis Keluar Feeder')->after('is_system');
            $table->string('kode_status', 10)->unique(); // A, C, D, L
            $table->string('nama_status'); // Aktif, Cuti, Lulus

            // 3 Kolom Logika
            $table->boolean('is_pengajuan_mhs')->default(false); // Diajukan Mahasiswa?
            $table->boolean('is_aktif')->default(false); // Terhitung Aktif?
            $table->boolean('is_sks')->default(false); // Boleh ambil SKS/Kuliah?

            // Flag System (Biar gak bisa dihapus sembarangan)
            $table->boolean('is_system')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_status_mahasiswa');
    }
};
