<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('name');
            $table->string('type')->comment('Item, Service');
            $table->string('type_option')->comment('sku, wo sku, onetime, monthly, annualy');
            $table->string('description');
            $table->string('spec');
            $table->string('mapping_code');
            $table->string('unit_price')->comment('General Discount/Daily Discount, FS Discount/FS Price');
            $table->string('way_import');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
