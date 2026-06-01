<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The HandlingSession model + Chat Livewire components reference a
 * `handling_sessions` table that has no migration in the repo (schema drift).
 *
 * Columns derived from actual usage:
 *  - ChatBox/ChatComponent: HandlingSession::create(['client_id','agent_id','user_id'])
 *  - ChatSlug/ChatBox: $session->view_transcript = 'requested'|'approve'|'reject'
 *    and is_null($session->view_transcript) -> so view_transcript is a NULLABLE STRING.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('handling_sessions')) {
            return;
        }

        Schema::create('handling_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable()->index();           // client uuid/id
            $table->unsignedBigInteger('agent_id')->nullable()->index(); // handling user
            $table->unsignedBigInteger('user_id')->nullable()->index();  // owner user
            $table->string('view_transcript')->nullable();              // null|requested|approve|reject
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handling_sessions');
    }
};
