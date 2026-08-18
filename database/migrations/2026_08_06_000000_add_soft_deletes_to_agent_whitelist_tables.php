<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soft-delete protection for every AI-agent whitelisted table.
 *
 * orders / projects / contracts / tasks / tickets already had a deleted_at
 * column; this fills the remaining gap (blast_messages, quotations, billings)
 * so a delete via the agent or the regular UI can never permanently destroy
 * data — it is always recoverable.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['blast_messages', 'quotations', 'billings'] as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['blast_messages', 'quotations', 'billings'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
