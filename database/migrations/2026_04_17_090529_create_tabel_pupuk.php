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
        Schema::create('tabel_pupuk', function (Blueprint $table) {
            $table->id('id_pupuk');
            $table->string('nama_pupuk', 50);
            $table->decimal('harga_subsidi', 10, 2);
            $table->integer('stok');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_pupuk');
    }
};
