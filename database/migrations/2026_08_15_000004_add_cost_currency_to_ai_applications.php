<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ai_applications')) {
            return;
        }

        if (! Schema::hasColumn('ai_applications', 'cost_currency')) {
            Schema::table('ai_applications', function (Blueprint $table) {
                $table->string('cost_currency', 8)->default('USD')->after('monthly_cost_limit');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ai_applications') && Schema::hasColumn('ai_applications', 'cost_currency')) {
            Schema::table('ai_applications', function (Blueprint $table) {
                $table->dropColumn('cost_currency');
            });
        }
    }
};
