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
        Schema::create('siakad_master_kurikulum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_kurikulum', 150);
            $table->text('deskripsi')->nullable();
            $table->enum('isactive', [1, 0])->default(1);
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_kurikulum');
    }
};
