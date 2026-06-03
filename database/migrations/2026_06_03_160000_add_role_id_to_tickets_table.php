<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable()->after('handled_by');
                $table->index('role_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'role_id')) {
                $table->dropIndex(['role_id']);
                $table->dropColumn('role_id');
            }
        });
    }
};
