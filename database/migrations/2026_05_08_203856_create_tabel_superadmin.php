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
        Schema::create('tabel_superadmin', function (Blueprint $table) {
            $table->id('id_superadmin');
            $table->foreignId('id_user')->constrained('tabel_users', 'id_user')->cascadeOnDelete();
            $table->string('nama_superadmin',50);
            $table->string('nip',18)->unique();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_superadmin');
    }
};
