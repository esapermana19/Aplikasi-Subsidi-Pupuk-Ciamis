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
        Schema::table('tabel_transaksi', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('id_musim')->nullable()->after('id_mitra');
            $table->foreign('id_musim')->references('id_musim')->on('tabel_musim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_transaksi', function (Blueprint $table) {
            //
            $table->dropForeign(['id_musim']);
            $table->dropColumn('id_musim');
        });
    }
};
