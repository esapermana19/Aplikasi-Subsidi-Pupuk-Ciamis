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
        Schema::create('tabel_mitra', function (Blueprint $table) {
            $table->id('id_mitra');
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->onDelete('cascade');
            $table->string('nama_mitra', 50);
            $table->string('nama_pemilik', 50);
            $table->string('nik', 16)->unique();
            $table->string('alamat_mitra', 50);
            $table->string('no_rek', 20);
            $table->decimal('saldo_app', 15, 2)->default(0); // decimal lebih aman untuk uang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_mitra');
    }
};
