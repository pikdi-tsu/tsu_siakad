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
        Schema::create('siakad_master_data_perguruan_tinggi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_unit');
            $table->string('nama_unit');
            $table->string('nama_unit_en');
            $table->string('nama_singkat');
            $table->string('jenis_perguruan_tinggi');
            $table->string('lembaga_naungan');
            $table->string('unit_satuan_kerja');
            $table->year('periode_berdiri');
            $table->string('no_sk_pendirian');
            $table->date('tanggal_sk_pendirian');

            $table->string('rektor');
            $table->string('wakil_rektor1')->nullable();
            $table->string('wakil_rektor2')->nullable();
            $table->string('wakil_rektor3')->nullable();
            $table->string('wakil_rektor4')->nullable();

            $table->string('lembaga_akreditasi');
            $table->string('peringkat_akreditasi');
            $table->decimal('nilai_akreditasi', 5, 2)->nullable();
            $table->string('no_sk_akreditasi');
            $table->date('tanggal_sk_akreditasi');
            $table->date('tanggal_berlaku_akreditasi');
            $table->date('tanggal_berakhir_akreditasi');

            $table->text('file_sertifikat_akreditasi')->nullable();
            $table->text('visi');
            $table->text('misi');
            $table->text('alamat');

            $table->string('telepon');
            $table->string('alamat_email');
            $table->string('alamat_website');
            $table->string('fax')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_data_perguruan_tinggi');
    }
};
