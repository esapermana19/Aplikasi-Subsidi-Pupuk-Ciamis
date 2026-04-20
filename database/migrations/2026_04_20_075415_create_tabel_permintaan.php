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
        Schema::create('tabel_permintaan', function (Blueprint $table) {
            $table->id('id_permintaan'); // Primary Key

            // Relasi ke tabel_mitra (id_mitra)
            $table->unsignedBigInteger('id_mitra');
            $table->foreign('id_mitra')->references('id_mitra')->on('tabel_mitra')->onDelete('cascade');

            // Relasi ke tabel_admin (id_admin) - Nullable karena saat dibuat belum di-approve admin
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->foreign('id_admin')->references('id_admin')->on('tabel_admin')->onDelete('set null');

            $table->date('tgl_permintaan');
            $table->enum('status_permintaan', ['pending', 'diproses', 'disetujui','diterima', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable(); // Tambahan untuk alasan penolakan atau instruksi admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_permintaan');
    }
};
