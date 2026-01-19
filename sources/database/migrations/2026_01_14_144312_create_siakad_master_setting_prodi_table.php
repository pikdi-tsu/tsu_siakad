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
        Schema::create('siakad_master_setting_prodi', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi (Sesuaikan nama tabel master kamu)
            $table->uuid('id_periode')->index();
            $table->uuid('id_prodi')->index();
            $table->uuid('id_kurikulum_maba')->nullable(); // Dropdown Kurikulum

            // Konfigurasi Checkbox
            $table->boolean('is_biodata')->default(false);
            $table->boolean('is_krs')->default(false);
            $table->boolean('is_validasi_krs')->default(false);
            $table->boolean('is_cetak_krs')->default(false);
            $table->boolean('is_khs')->default(false);
            $table->boolean('is_nilai')->default(false);
            $table->boolean('is_kuesioner')->default(false);
            $table->boolean('is_generate_pertemuan')->default(false);

            // Constraint: 1 Prodi cuma punya 1 setting di 1 periode
            $table->unique(['id_periode', 'id_prodi']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_setting_prodi');
    }
};
