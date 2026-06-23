<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // One row per chat session (conversation) in the AI Console.
        if (! Schema::hasTable('agent_chats')) {
            Schema::create('agent_chats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->index();
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        // Each request/response turn stored against its session.
        if (! Schema::hasTable('agent_chat_messages')) {
            Schema::create('agent_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agent_chat_id')->index();
                $table->string('role'); // user | assistant
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('agent_chat_messages');
        Schema::dropIfExists('agent_chats');
    }
};
