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
        Schema::create('siakad_master_jabatan_struktural', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_jabatan_struktural', 150)->unique();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')
                ->references('id')
                ->on('siakad_master_jabatan_struktural')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_jabatan_struktural');
    }
};
