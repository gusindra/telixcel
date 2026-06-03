<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('flow_processes', function (Blueprint $table) {
            if (! Schema::hasColumn('flow_processes', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('model_id');
                $table->index('role_id');
            }
        });
    }

    public function down()
    {
        Schema::table('flow_processes', function (Blueprint $table) {
            if (Schema::hasColumn('flow_processes', 'role_id')) {
                $table->dropIndex(['role_id']);
                $table->dropColumn('role_id');
            }
        });
    }
};
