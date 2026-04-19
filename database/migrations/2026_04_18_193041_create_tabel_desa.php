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
        Schema::create('tabel_desa', function (Blueprint $table) {
            $table->id('id_desa');
            $table->unsignedBigInteger('id_kecamatan'); // Foreign Key
            $table->string('nama_desa');
            $table->timestamps();

            $table->foreign('id_kecamatan')->references('id_kecamatan')->on('tabel_kecamatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_desa');
    }
};
