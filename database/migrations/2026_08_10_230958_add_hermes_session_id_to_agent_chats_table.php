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
            if (! Schema::hasColumn('agent_chats', 'hermes_session_id')) {
                $table->string('hermes_session_id', 128)->nullable()->after('title')->index();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('agent_chats')) {
            return;
        }

        Schema::table('agent_chats', function (Blueprint $table) {
            if (Schema::hasColumn('agent_chats', 'hermes_session_id')) {
                $table->dropColumn('hermes_session_id');
            }
        });
    }
};
