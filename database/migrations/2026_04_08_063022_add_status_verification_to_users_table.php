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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_akun', ['pending', 'aktif', 'ditolak'])->default('pending')->after('role');
            $table->string('alasan_penolakan')->nullable()->after('status_akun');
            $table->foreignId('verified_by')->nullable()->after('alasan_penolakan')
                ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status_akun','alasan_penolakan','verified_by']);
        });
    }
};
