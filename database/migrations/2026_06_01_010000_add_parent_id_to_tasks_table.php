<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tasks become a tree: parent_id = 0 for root tasks, or the parent task id for children.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks') && ! Schema::hasColumn('tasks', 'parent_id')) {
            Schema::table('tasks', function (Blueprint $t) {
                $t->unsignedBigInteger('parent_id')->default(0)->index()->after('project_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'parent_id')) {
            Schema::table('tasks', function (Blueprint $t) {
                $t->dropColumn('parent_id');
            });
        }
    }
};
