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
        Schema::create('tabel_detail_stok', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_pupuk')->constrained('tabel_pupuk', 'id_pupuk')->onDelete('cascade');

            // Relasi opsional: Bisa diisi Admin (jika restock) atau Detail Transaksi (jika penjualan)
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->unsignedBigInteger('id_detail_transaksi')->nullable();

            $table->integer('stok_awal');
            $table->integer('jml_perubahan');
            $table->integer('stok_akhir');
            $table->string('ket');
            $table->timestamp('created_at')->useCurrent();

            // Foreign key manual agar aman
            $table->foreign('id_admin')->references('id_admin')->on('tabel_admin')->onDelete('set null');
            $table->foreign('id_detail_transaksi')->references('id_detail')->on('tabel_detail_transaksi')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_detail_stok');
    }
};
