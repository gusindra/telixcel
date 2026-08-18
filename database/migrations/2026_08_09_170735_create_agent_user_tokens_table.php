<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('agent_user_tokens')) {
            return;
        }

        Schema::create('agent_user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->index();
            // sha256 of plain token for lookup
            $table->string('token_hash', 64)->unique();
            // prefix for display/debug (e.g. agt_ab12…)
            $table->string('token_prefix', 16)->nullable();
            // encrypted plain token so Laravel can re-inject into AI context
            $table->text('token_encrypted');
            // JSON abilities: read, update_limited
            $table->json('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_user_tokens');
    }
};
