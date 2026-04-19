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
        Schema::table('tabel_mitra', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kecamatan')->after('nik');
            $table->unsignedBigInteger('id_desa')->after('id_kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_mitra', function (Blueprint $table) {
            $table->dropColumn('id_kecamatan');
            $table->dropColumn('id_desa');
        });
    }
};
