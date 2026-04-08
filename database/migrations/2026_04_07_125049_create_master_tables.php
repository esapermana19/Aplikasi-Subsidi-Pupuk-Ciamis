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
        //Tabel Pupuk
        Schema::create('pupuk', function (Blueprint $table) {
            $table->id('id_pupuk');
            $table->string('nama_pupuk', 50);
            $table->decimal('harga_subsidi', 15, 2);
            $table->decimal('stok', 15, 2)->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupuk');
    }
};
