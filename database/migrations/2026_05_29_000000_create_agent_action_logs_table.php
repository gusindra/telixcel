<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_action_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('tool');                 // query_records / create_record / update_record / delete_record
            $table->string('model_key')->nullable(); // order / project / ...
            $table->json('arguments')->nullable();
            $table->json('result')->nullable();
            $table->string('status');               // ok / proposed / approved / error

            // LLM metrics — used for the cloud-vs-local benchmark (task 2).
            $table->string('llm_model')->nullable();
            $table->unsignedBigInteger('total_duration_ns')->nullable();
            $table->unsignedInteger('eval_count')->nullable();
            $table->unsignedBigInteger('eval_duration_ns')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_action_logs');
    }
};
