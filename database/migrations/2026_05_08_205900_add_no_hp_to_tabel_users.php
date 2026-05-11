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
        Schema::table('tabel_users', function (Blueprint $table) {
            $table->string('no_hp', 15)->unique()->nullable()->after('status_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_users', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });
    }
};
