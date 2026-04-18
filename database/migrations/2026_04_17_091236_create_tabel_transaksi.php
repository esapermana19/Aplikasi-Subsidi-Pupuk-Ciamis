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
        Schema::create('tabel_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            // Relasi ke Petani dan Mitra
            $table->foreignId('id_petani')->constrained('tabel_petani', 'id_petani');
            $table->foreignId('id_mitra')->constrained('tabel_mitra', 'id_mitra');

            $table->date('tgl_transaksi');
            $table->enum('status_pembayaran', ['pending', 'success', 'failed'])->default('pending');
            $table->enum('status_pengambilan', ['sudah', 'belum'])->default('belum');
            $table->string('qr_code')->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_transaksi');
    }
};
