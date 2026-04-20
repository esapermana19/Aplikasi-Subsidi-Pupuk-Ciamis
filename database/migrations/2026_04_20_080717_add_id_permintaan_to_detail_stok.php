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
        Schema::table('tabel_detail_stok', function (Blueprint $table) {
            // Menambahkan kolom untuk menghubungkan riwayat stok dengan detail permintaan
            $table->unsignedBigInteger('id_detail_permintaan')->nullable()->after('id_detail_transaksi');
            $table->foreign('id_detail_permintaan')->references('id_detail_permintaan')->on('tabel_detail_permintaan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_stok', function (Blueprint $table) {
            $table->dropForeign(['id_detail_permintaan']);
            $table->dropColumn('id_detail_permintaan');
        });
    }
};
