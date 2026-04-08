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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique()->nullable(); // Untuk Admin/Superadmin
        $table->string('nik_nip', 18)->unique(); // NIK Petani/Mitra atau NIP Admin
        $table->string('password');

        // Tambahkan Role sesuai kebutuhan Anda
        $table->enum('role', ['superadmin', 'admin', 'petani', 'mitra']);

        // Kolom opsional (Nullable) karena tidak semua role punya atribut ini
        $table->string('alamat')->nullable();
        $table->string('no_rek', 20)->nullable();
        $table->decimal('saldo_app', 15, 2)->default(0);
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
