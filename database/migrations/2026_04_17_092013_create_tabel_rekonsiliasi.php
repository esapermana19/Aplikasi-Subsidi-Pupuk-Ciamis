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
        Schema::create('tabel_rekonsiliasi', function (Blueprint $table) {
            $table->id('id_rekonsiliasi');
            $table->foreignId('id_transaksi')->constrained('tabel_transaksi', 'id_transaksi')->onDelete('cascade');
            $table->foreignId('id_admin')->constrained('tabel_admin', 'id_admin')->onDelete('cascade');
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
        Schema::dropIfExists('tabel_rekonsiliasi');
    }
};
