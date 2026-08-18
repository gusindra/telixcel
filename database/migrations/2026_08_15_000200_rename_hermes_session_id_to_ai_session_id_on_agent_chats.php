<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('agent_chats')) {
            return;
        }

        Schema::table('agent_chats', function (Blueprint $table) {
            if (Schema::hasColumn('agent_chats', 'hermes_session_id') && ! Schema::hasColumn('agent_chats', 'ai_session_id')) {
                $table->renameColumn('hermes_session_id', 'ai_session_id');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('agent_chats')) {
            return;
        }

        Schema::table('agent_chats', function (Blueprint $table) {
            if (Schema::hasColumn('agent_chats', 'ai_session_id') && ! Schema::hasColumn('agent_chats', 'hermes_session_id')) {
                $table->renameColumn('ai_session_id', 'hermes_session_id');
            }
        });
    }
};
