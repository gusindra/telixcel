<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'ticket_id')) {
                $table->unsignedBigInteger('ticket_id')->nullable()->after('parent_id');
                $table->index('ticket_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'ticket_id')) {
                $table->dropIndex(['ticket_id']);
                $table->dropColumn('ticket_id');
            }
        });
    }
};
