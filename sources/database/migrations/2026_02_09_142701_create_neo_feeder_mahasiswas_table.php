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
        Schema::create('neo_feeder_mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID Lokal TSU

            // id_mahasiswa (Feeder): Untuk update biodata
            $table->uuid('id_mahasiswa_feeder')->nullable()->index();
            // id_registrasi_mahasiswa (Feeder): Untuk update KRS, Nilai, AKM
            $table->uuid('id_registrasi_mahasiswa_feeder')->nullable()->index();

            // Data Utama (Sesuai JSON Feeder)
            $table->string('nim')->unique(); // nipd di JSON
            $table->string('nama_mahasiswa');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir'); // Perlu parsing dari dd-mm-yyyy
            $table->string('tempat_lahir')->nullable();

            // Data Akademik
            $table->string('id_prodi_feeder')->nullable(); // id_sms / id_prodi di JSON
            $table->string('nama_program_studi')->nullable();
            $table->string('id_periode_masuk', 5)->nullable(); // 20241, 20242
            $table->string('nama_periode_masuk')->nullable(); // "2024/2025 Genap"
            $table->string('id_status_mahasiswa')->nullable(); // "A" (Aktif), "L" (Lulus), "4" (Keluar)
            $table->string('nama_status_mahasiswa')->nullable(); // "Aktif", "Lulus"

            // Data Pelengkap
            $table->integer('id_agama')->nullable();
            $table->string('nama_agama')->nullable();
            $table->decimal('ipk', 4, 2)->default(0.00); // 3.58
            $table->integer('total_sks')->default(0); // 145
            $table->timestamp('last_synced_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neo_feeder_mahasiswas');
    }
};
