<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'priority')) {
                $table->string('priority')->nullable()->default('medium')->after('status');
            }
            if (! Schema::hasColumn('tickets', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
            if (! Schema::hasColumn('tickets', 'closed_at')) {
                $table->timestamp('closed_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            foreach (['priority', 'resolved_at', 'closed_at'] as $col) {
                if (Schema::hasColumn('tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
