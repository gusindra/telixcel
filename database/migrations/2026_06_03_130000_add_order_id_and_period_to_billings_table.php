<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            if (! Schema::hasColumn('billings', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('id');
                $table->index('order_id');
            }
            if (! Schema::hasColumn('billings', 'period')) {
                $table->string('period')->nullable()->after('invoice_date');
            }
        });
    }

    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            if (Schema::hasColumn('billings', 'order_id')) {
                $table->dropIndex(['order_id']);
                $table->dropColumn('order_id');
            }
            if (Schema::hasColumn('billings', 'period')) {
                $table->dropColumn('period');
            }
        });
    }
};
