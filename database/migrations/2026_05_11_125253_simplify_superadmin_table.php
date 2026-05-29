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
            $table->string('nama', 50)->nullable()->after('email');
            $table->string('nip', 18)->nullable()->after('nama');
        });

        Schema::dropIfExists('tabel_superadmin');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tabel_superadmin', function (Blueprint $table) {
            $table->id('id_superadmin');
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->cascadeOnDelete();
            $table->string('nama_superadmin', 50);
            $table->string('nip', 18)->unique();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        Schema::table('tabel_users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'nip']);
        });
    }
};
