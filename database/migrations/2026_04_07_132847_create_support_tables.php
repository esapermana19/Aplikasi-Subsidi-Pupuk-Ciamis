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
        // Tabel Pencairan
        Schema::create('pencairan', function (Blueprint $table) {
            $table->id('id_pencairan');
            // Diganti ke 'users' karena data Mitra ada di sana
            $table->foreignId('id_mitra')->constrained('users')->onDelete('restrict');
            $table->decimal('jml_transfer', 15, 2);
            $table->date('tgl_transfer');
            $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
            $table->timestamps();
        });

        // Tabel Rekonsiliasi
        Schema::create('rekonsiliasi', function (Blueprint $table) {
            $table->id('id_rekonsiliasi');
            $table->foreignId('id_transaksi')->constrained('transaksi', 'id_transaksi');
            // Diganti ke 'users' karena data Admin ada di sana
            $table->foreignId('id_admin')->constrained('users')->onDelete('restrict');
            $table->date('tgl_verifikasi');
            $table->enum('status', ['match', 'mismatch']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan');
        Schema::dropIfExists('rekonsiliasi');
    }
};
