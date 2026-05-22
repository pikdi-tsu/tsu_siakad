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

        Schema::create($tableName . '_master_pembiayaan', static function (Blueprint $table) use ($tableUsers) {
            $table->uuid('id')->primary();
            $table->integer('id_neofeeder')->nullable()->comment('ID Mapping ke Dictionary Pembiayaan Feeder');
            $table->string('nama_pembiayaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_master_pembiayaan');
    }
};
