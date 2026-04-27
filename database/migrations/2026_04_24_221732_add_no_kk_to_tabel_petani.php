<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tabel_petani', function (Blueprint $blueprint) {
            // Menambahkan kolom no_kk, tipe string (karena 16 digit), bersifat unik, dan diletakkan setelah kolom nik
            $blueprint->string('no_kk', 16)->unique()->after('nik');
        });
    }

    public function down(): void
    {
        Schema::table('tabel_petani', function (Blueprint $blueprint) {
            $blueprint->dropColumn('no_kk');
        });
    }
};
