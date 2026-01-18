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
        $tableUsers = config('auth.providers.users.table', 'users');
        $tableName = config('app.module.name', 'siakad');

        Schema::create($tableName . '_data_mahasiswas', static function (Blueprint $table) use ($tableUsers) {
            $table->uuid('id')->primary();
            // RELASI KE AUTH (SSO)
            $table->foreignUuid('user_id')->constrained($tableUsers)->onDelete('cascade');

            // --- DATA AKADEMIK ---
            $table->string('nim', 20)->unique();

            // Relasi ke Master Prodi (pmb_master_jurusankuliah -> id [bigint unsigned])
            $table->unsignedBigInteger('id_prodi')->nullable();

            // Relasi ke Master Jenjang (pmb_master_jenjang -> id [bigint unsigned])
            $table->unsignedBigInteger('id_jenjang')->nullable();

            // Relasi ke Waktu Kuliah (Pagi/Sore/Karyawan)
            $table->bigInteger('id_waktu_kuliah')->nullable();

            $table->string('angkatan', 4);
            $table->string('status_akademik')->default('AKTIF'); // AKTIF, CUTI, DO, LULUS
            $table->string('jalur_masuk')->nullable(); // SBMPTN, MANDIRI, PRESTASI (Bisa ambil dari pmb_master_beasiswa/jalur)

            // --- DATA PRIBADI (Diambil dari pmb_biodata) ---
            $table->string('nik_ktp', 100)->unique()->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable(); // L/P
            $table->string('agama', 20)->nullable();
            $table->string('no_hp', 25)->nullable();
            $table->string('email_pribadi')->nullable(); // Cadangan selain email kampus

            // --- DATA WILAYAH (Sesuai Master PMB) ---
            $table->char('id_provinsi', 2)->collation('utf8mb4_0900_ai_ci')->nullable();
            $table->unsignedBigInteger('id_kabupaten')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('kodepos', 10)->nullable();

            // --- DATA ORANG TUA ---
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('no_hp_ortu', 25)->nullable();

            $table->timestamps();

            // --- DEFINISI FOREIGN KEY ---
            $table->foreign('id_prodi')
                ->references('id')->on('pmb_master_jurusankuliah')
                ->onDelete('restrict');
            $table->foreign('id_jenjang')
                ->references('id')->on('pmb_master_jenjang')
                ->onDelete('restrict');
            if (Schema::hasTable('pmb_master_provinsi')) {
                $table->foreign('id_provinsi')
                    ->references('idprov')->on('pmb_master_provinsi')
                    ->onDelete('set null');
            }
            if (Schema::hasTable('pmb_master_kabupaten')) {
                $table->foreign('id_kabupaten')
                    ->references('id')->on('pmb_master_kabupaten')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists(config('app.module.name', 'siakad') . '_data_mahasiswas');
    }
};
