<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Reason/comment recorded when a task is set to a terminal status
            // (declined / cancelled / aborted), which require a mandatory comment.
            if (! Schema::hasColumn('tasks', 'status_note')) {
                $table->text('status_note')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'status_note')) {
                $table->dropColumn('status_note');
            }
        });
    }
};
