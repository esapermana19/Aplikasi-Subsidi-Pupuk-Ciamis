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
        Schema::create('tabel_petani', function (Blueprint $table) {
            $table->id('id_petani');
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('nama_petani', 50);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat_petani', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_petani');
    }
};
