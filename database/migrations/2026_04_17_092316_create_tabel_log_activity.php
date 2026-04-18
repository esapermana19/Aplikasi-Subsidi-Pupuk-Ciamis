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
        Schema::create('tabel_log_activity', function (Blueprint $table) {
            $table->id('id_log');
            // Relasi ke tabel_users agar tahu siapa pelakunya
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->onDelete('cascade');

            $table->string('aktivitas'); // Contoh: "Menambah Stok Pupuk", "Melakukan Transaksi"
            $table->string('fitur');    // Contoh: "Manajemen Pupuk", "Pembayaran"
            $table->text('detail_perubahan')->nullable(); // Untuk menyimpan data lama vs data baru (format JSON)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable(); // Informasi browser/perangkat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_log_activity');
    }
};
