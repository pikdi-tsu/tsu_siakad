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
        Schema::create('siakad_master_program_studi', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas Prodi (sesuai feeder)
            $table->string('kode_prodi', 20)->unique();
            $table->string('nama_prodi', 200);
            $table->string('jenjang', 5);

            // Penghubung Neo Feeder
            $table->uuid('id_prodi_feeder')->nullable()->comment('Kabel penghubung ke UUID Neo Feeder');

            // Pimpinan Prodi
            $table->string('ketua_prodi', 150)->nullable();

            // Relasi Fakultas
            $table->uuid('fakultas_id')->nullable();

            // Status Prodi (nilai baku PDDIKTI)
            $table->enum('status_prodi', ['Aktif', 'Tidak Aktif', 'Tutup']);

            $table->timestamps();

            // Foreign Key
            $table->foreign('fakultas_id')
                ->references('id')
                ->on('siakad_master_fakultas')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_program_studi');
    }
};
