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
        Schema::create('tabel_users', function (Blueprint $table) {
            $table->id('id_user'); // Ubah dari id() jadi id('id_user')
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['Petani', 'Mitra', 'Admin']);
            $table->enum('status_akun', ['aktif', 'nonaktif', 'pending', 'ditolak'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_users');
    }
};
