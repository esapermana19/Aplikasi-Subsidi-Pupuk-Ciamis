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
        Schema::table('tabel_detail_permintaan', function (Blueprint $table) {
            $table->dropForeign(['id_permintaan']);
        });

        Schema::table('tabel_permintaan', function (Blueprint $table) {
            $table->string('id_permintaan', 20)->change();
        });

        Schema::table('tabel_detail_permintaan', function (Blueprint $table) {
            $table->string('id_permintaan', 20)->change();
            $table->foreign('id_permintaan')->references('id_permintaan')->on('tabel_permintaan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_detail_permintaan', function (Blueprint $table) {
            $table->dropForeign(['id_permintaan']);
        });

        Schema::table('tabel_permintaan', function (Blueprint $table) {
            $table->bigIncrements('id_permintaan')->change();
        });

        Schema::table('tabel_detail_permintaan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_permintaan')->change();
            $table->foreign('id_permintaan')->references('id_permintaan')->on('tabel_permintaan')->onDelete('cascade');
        });
    }
};
