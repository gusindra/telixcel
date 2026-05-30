<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix migration: creates tables that were missing from the repo.
 *
 * 1. tickets  — original migration (2021_10_29_091103_create_tikets_table.php)
 *               had its entire up() body commented out so the table was never
 *               created even though the migration shows as "Ran".
 *               Columns from Ticket::$fillable + SoftDeletes.
 *
 * 2. agent_action_logs — created by Telixcel AI Console feature (task 1).
 *                        Holds audit log of agent tool calls + LLM metrics
 *                        for cloud-vs-local benchmark (task 2).
 *
 * Both are guarded with hasTable() so this migration is safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. tickets ────────────────────────────────────────────────────────
        if (! Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('reasons')->nullable();
                $table->text('solution')->nullable();
                $table->string('status')->default('open');       // open|handle|waiting
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->unsignedBigInteger('request_id')->nullable()->index();
                $table->unsignedBigInteger('handled_by')->nullable();
                $table->unsignedBigInteger('forward_to')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // ── 2. agent_action_logs ──────────────────────────────────────────────
        if (! Schema::hasTable('agent_action_logs')) {
            Schema::create('agent_action_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('tool');
                $table->string('model_key')->nullable();
                $table->json('arguments')->nullable();
                $table->json('result')->nullable();
                $table->string('status');

                // LLM metrics for cloud-vs-local benchmark (task 2)
                $table->string('llm_model')->nullable();
                $table->unsignedBigInteger('total_duration_ns')->nullable();
                $table->unsignedInteger('eval_count')->nullable();
                $table->unsignedBigInteger('eval_duration_ns')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_action_logs');
        Schema::dropIfExists('tickets');
    }
};
