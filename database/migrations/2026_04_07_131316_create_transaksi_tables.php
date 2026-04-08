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
        //Tabel Transaksi
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');

            // Keduanya merujuk ke tabel 'users'
            $table->foreignId('id_petani')->constrained('users')->onDelete('restrict');
            $table->foreignId('id_mitra')->constrained('users')->onDelete('restrict');

            $table->date('tgl_transaksi');
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', ['pending', 'selesai', 'dibatalkan'])->default('pending');
            $table->enum('status_pengambilan', ['belum', 'sudah'])->default('belum');
            $table->string('qr_code')->unique();
            $table->timestamps();
        });

        //Tabel Detail Transaksi
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi')->onDelete('restrict');
            $table->foreignId('id_pupuk')->constrained('pupuk', 'id_pupuk')->onDelete('restrict');
            $table->integer('jml_beli');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
