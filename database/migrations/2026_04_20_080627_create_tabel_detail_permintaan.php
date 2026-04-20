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
        Schema::create('tabel_detail_permintaan', function (Blueprint $table) {
            $table->id('id_detail_permintaan');

            // Relasi ke tabel_permintaan yang baru dibuat
            $table->unsignedBigInteger('id_permintaan');
            $table->foreign('id_permintaan')->references('id_permintaan')->on('tabel_permintaan')->onDelete('cascade');

            // Relasi ke tabel_pupuk (id_pupuk)
            $table->unsignedBigInteger('id_pupuk');
            $table->foreign('id_pupuk')->references('id_pupuk')->on('tabel_pupuk')->onDelete('cascade');

            $table->integer('jml_diminta');
            $table->integer('jml_disetujui')->default(0); // Diisi oleh admin saat approval
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_detail_permintaan');
    }
};
