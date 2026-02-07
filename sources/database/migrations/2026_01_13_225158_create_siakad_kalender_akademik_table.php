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
        $tableName = config('app.module.name', 'siakad');

        Schema::create($tableName . '_master_kalender_akademik', function (Blueprint $table) use ($tableName) {
            $table->uuid('id')->primary();
            // Relasi ke Master Kegiatan (yang barusan kita buat)
            $table->foreignUuid('id_kegiatan')
                ->constrained($tableName . '_master_kegiatan_akademik')
                ->onDelete('cascade'); // Hapus kalender kalau master kegiatannya dihapus

            // Periode (Nanti bisa direlasikan ke Master Periode jika sudah ada)
            // Sementara kita pakai String dulu (misal: "20251" untuk 2025 Ganjil)
            $table->string('id_periode', 10)->nullable()->index();

            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->string('keterangan')->nullable();

            // Flag Libur
            $table->boolean('is_libur_nasional')->default(false);
            $table->boolean('is_libur_akademik')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('app.module.name', 'siakad');

        Schema::dropIfExists($tableName . '_master_kalender_akademik');
    }
};
