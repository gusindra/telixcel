<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('flow_settings', 'result_status')) {
            Schema::table('flow_settings', function (Blueprint $table) {
                $table->string('result_status', 100)->nullable()->after('after_status');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('flow_settings', 'result_status')) {
            Schema::table('flow_settings', function (Blueprint $table) {
                $table->dropColumn('result_status');
            });
        }
    }
};
