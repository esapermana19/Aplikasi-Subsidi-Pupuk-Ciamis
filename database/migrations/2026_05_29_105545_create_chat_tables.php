<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabel_chats', function (Blueprint $table) {
            $table->id('id_chat');
            $table->unsignedBigInteger('id_user'); // Petani or Mitra
            $table->string('topik');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('tabel_users')->onDelete('cascade');
        });

        Schema::create('tabel_chat_messages', function (Blueprint $table) {
            $table->id('id_message');
            $table->unsignedBigInteger('id_chat');
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_chat')->references('id_chat')->on('tabel_chats')->onDelete('cascade');
            $table->foreign('sender_id')->references('id_user')->on('tabel_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_chat_messages');
        Schema::dropIfExists('tabel_chats');
    }
};
