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
        Schema::create('tabel_pencairan', function (Blueprint $table) {
            $table->id('id_pencairan');
            $table->foreignId('id_mitra')->constrained('tabel_mitra', 'id_mitra')->onDelete('cascade');
            $table->decimal('jml_transfer', 15, 2);
            $table->date('tgl_transfer');
            $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_pencairan');
    }
};
