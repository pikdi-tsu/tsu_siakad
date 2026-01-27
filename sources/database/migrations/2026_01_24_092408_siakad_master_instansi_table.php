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
        Schema::create('siakad_master_instansi', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Identitas instansi
            $table->string('kode_instansi', 30)->unique();
            $table->string('nama_instansi', 200);

            // Alamat & kontak
            $table->text('alamat');
            $table->string('no_telepon', 30)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_instansi');
    }
};
