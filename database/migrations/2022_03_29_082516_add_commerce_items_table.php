<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commerce_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku');
            $table->string('name');
            $table->string('spec')->nullable();
            $table->string('source_id')->nullable();
            $table->string('type', 100)->nullable()->comment('sku, without_sku, one_time, monthly, anually');
            $table->string('unit', 50)->nullable();
            $table->text('description')->nullable();
            $table->float('general_discount')->default(0);
            $table->float('fs_price')->default(0);
            $table->float('unit_price')->default(0);
            $table->string('product_line')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('way_import', 20)->nullable()->comment('fob, ddp');
            $table->string('status', 100)->default('active')->comment('active, disabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commerce_items');
    }
};
