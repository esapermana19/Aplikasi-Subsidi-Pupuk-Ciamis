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
        Schema::create('tabel_musim', function (Blueprint $table) {
            $table->id('id_musim');
            $table->string('nama_musim'); // Contoh: MT-1 2026 (Penghujan)
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->decimal('kuota_global', 12, 2); // Total subsidi seluruh wilayah (Kg)
            $table->decimal('limit_per_petani', 8, 2); // Jatah maksimal per petani (Kg)
            $table->boolean('is_active')->default(false); // Hanya boleh ada satu musim aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_musim');
    }
};
