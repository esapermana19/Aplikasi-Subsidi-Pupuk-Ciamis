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
        Schema::create('tabel_riwayat_laporan', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->unsignedBigInteger('id_admin');
            $table->string('jenis_laporan');
            $table->string('nama_laporan');
            $table->date('periode_start');
            $table->date('periode_end');
            $table->string('file_path');
            $table->string('file_size'); // misalnya '2.4 MB'
            $table->timestamps();

            $table->foreign('id_admin')->references('id_admin')->on('tabel_admin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_riwayat_laporan');
    }
};
