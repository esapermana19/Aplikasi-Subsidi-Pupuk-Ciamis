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
        Schema::table('tabel_pupuk', function (Blueprint $table) {
            $table->string('kode_pupuk', 5)->unique()->after('id_pupuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_pupuk', function (Blueprint $table) {
            $table->dropColumn('kode_pupuk');
        });
    }
};
