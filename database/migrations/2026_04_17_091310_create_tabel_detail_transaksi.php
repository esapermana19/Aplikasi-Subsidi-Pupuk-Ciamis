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
        Schema::create('tabel_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            // Relasi ke tabel_transaksi (Header) dan tabel_pupuk (Barang)
            $table->foreignId('id_transaksi')->constrained('tabel_transaksi', 'id_transaksi')->onDelete('cascade');
            $table->foreignId('id_pupuk')->constrained('tabel_pupuk', 'id_pupuk');

            $table->integer('jml_beli');
            $table->decimal('harga_satuan', 12, 2); // Penting untuk audit harga saat transaksi
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_detail_transaksi');
    }
};
