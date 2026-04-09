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
        $tableUsers = config('app.table.users');
        $tableName = config('app.table.data_mahasiswas');

        Schema::create($tableName, static function (Blueprint $table) use ($tableUsers) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained($tableUsers)->onDelete('cascade');

            // --- DATA AKADEMIK DASAR ---
            $table->string('nim', 20)->unique();
            $table->char('id_prodi', 36)->collation('utf8mb4_unicode_ci')->nullable();
            $table->char('id_jenjang', 36)->collation('utf8mb4_unicode_ci')->nullable();
            $table->bigInteger('id_waktu_kuliah')->nullable();
            $table->uuid('id_periode_masuk')->nullable();
            $table->uuid('id_status_mahasiswa')->nullable();

            // --- DATA PENDAFTARAN & PINDAHAN ---
            $table->integer('id_jenis_daftar')->default(1); // Relasi ke siakad_master_jenis_daftar
            $table->integer('id_jalur_daftar')->nullable();
            $table->integer('id_pembiayaan')->nullable(); // Relasi ke siakad_master_pembiayaan
            $table->date('tanggal_daftar')->nullable();

            // Khusus Mahasiswa Pindahan
            $table->char('id_perguruan_tinggi_asal', 36)->collation('utf8mb4_unicode_ci')->nullable();
            $table->char('id_prodi_asal', 36)->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('sks_diakui')->nullable();

            // --- DATA STATUS KELUAR ---
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan_keluar')->nullable();

            // --- DATA PRIBADI (FEEDER BIODATA) ---
            $table->string('nik_ktp', 100)->unique()->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->uuid('id_agama')->nullable();
            $table->string('kewarganegaraan', 100)->default('Indonesia')->nullable();
            $table->string('no_hp', 25)->nullable();
            $table->string('email_pribadi')->nullable();

            // --- DATA WILAYAH (DOMISILI) ---
            $table->char('id_provinsi', 2)->collation('utf8mb4_0900_ai_ci')->nullable();
            $table->unsignedBigInteger('id_kabupaten')->nullable();
            $table->unsignedBigInteger('id_wilayah')->nullable(); // ID Kecamatan Feeder
            $table->string('kelurahan')->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('kodepos', 10)->nullable();

            // --- DATA ORANG TUA, WALI & SOSIAL ---
            $table->string('nik_ayah', 20)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->date('tgl_lahir_ayah')->nullable();
            $table->integer('id_pendidikan_ayah')->nullable();
            $table->integer('id_pekerjaan_ayah')->nullable(); // Relasi ke master_pekerjaan
            $table->integer('id_penghasilan_ayah')->nullable();

            $table->string('nik_ibu', 20)->nullable();
            $table->string('nama_ibu')->nullable();
            $table->date('tgl_lahir_ibu')->nullable();
            $table->integer('id_pendidikan_ibu')->nullable();
            $table->integer('id_pekerjaan_ibu')->nullable(); // Relasi ke master_pekerjaan
            $table->integer('id_penghasilan_ibu')->nullable();

            $table->string('nama_wali')->nullable();
            $table->date('tgl_lahir_wali')->nullable();
            $table->integer('id_pendidikan_wali')->nullable();
            $table->integer('id_pekerjaan_wali')->nullable();
            $table->integer('id_penghasilan_wali')->nullable();

            $table->string('no_hp_ortu', 25)->nullable();
            $table->tinyInteger('penerima_kps')->default(0);

            $table->timestamps();

            // --- DEFINISI FOREIGN KEY ---

            if (Schema::hasTable('siakad_master_program_studi')) {
                $table->foreign('id_prodi')->references('id')->on('siakad_master_program_studi')->onDelete('restrict');
            }
            if (Schema::hasTable('siakad_periode_akademik')) {
                $table->foreign('id_periode_masuk')->references('id')->on('siakad_periode_akademik')->onDelete('set null');
            }
            if (Schema::hasTable('siakad_master_status_mahasiswa')) {
                $table->foreign('id_status_mahasiswa')->references('id')->on('siakad_master_status_mahasiswa')->onDelete('restrict');
            }
            if (Schema::hasTable('siakad_master_jenis_daftar')) {
                $table->foreign('id_jenis_daftar')->references('id')->on('siakad_master_jenis_daftar')->onDelete('set null');
            }
            if (Schema::hasTable('siakad_master_pembiayaan')) {
                $table->foreign('id_pembiayaan')->references('id')->on('siakad_master_pembiayaan')->onDelete('set null');
            }
            if (Schema::hasTable('siakad_master_pekerjaan')) {
                $table->foreign('id_pekerjaan_ayah')->references('id')->on('siakad_master_pekerjaan')->onDelete('set null');
                $table->foreign('id_pekerjaan_ibu')->references('id')->on('siakad_master_pekerjaan')->onDelete('set null');
                $table->foreign('id_pekerjaan_wali')->references('id')->on('siakad_master_pekerjaan')->onDelete('set null');
            }
            if (Schema::hasTable('siakad_master_tingkat_pendidikan_universitas')) {
                $table->foreign('id_jenjang')->references('id')->on('siakad_master_tingkat_pendidikan_universitas')->onDelete('restrict');
            }
            if (Schema::hasTable('pmb_master_provinsi')) {
                $table->foreign('id_provinsi')->references('idprov')->on('pmb_master_provinsi')->onDelete('set null');
            }
            if (Schema::hasTable('pmb_master_kabupaten')) {
                $table->foreign('id_kabupaten')->references('id')->on('pmb_master_kabupaten')->onDelete('set null');
            }
            if (Schema::hasTable('pmb_master_kecamatan')) {
                $table->foreign('id_wilayah')->references('id')->on('pmb_master_kecamatan')->onDelete('set null');
            }
            if (Schema::hasTable('pmb_master_waktukuliah')) {
                $table->foreign('id_waktu_kuliah')->references('id')->on('pmb_master_waktukuliah')->onDelete('set null');
            }
            if (Schema::hasTable('siakad_master_agama')) {
                $table->foreign('id_agama')->references('id')->on('siakad_master_agama')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        $tableName = config('app.table.data_mahasiswas');

        Schema::dropIfExists($tableName);
    }
};
