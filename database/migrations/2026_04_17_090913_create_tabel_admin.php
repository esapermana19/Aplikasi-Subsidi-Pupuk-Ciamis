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
        Schema::create('tabel_admin', function (Blueprint $table) {
            $table->id('id_admin');
            // Relasi ke tabel_users yang tadi sudah kita buat
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->onDelete('cascade');
            $table->string('nip', 18)->unique();
            $table->string('nama_admin', 50);
            $table->string('email'); // Sesuai ERD terbaru kamu
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        // Tambahkan relasi verified_by di sini untuk menghindari circular dependency
        Schema::table('tabel_users', function (Blueprint $table) {
            $table->foreign('verified_by')->references('id_admin')->on('tabel_admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_admin');
    }
};
