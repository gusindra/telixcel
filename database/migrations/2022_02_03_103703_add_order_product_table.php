<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->nullable();
            $table->string('model')->nullable();
            $table->string('model_id')->nullable();
            $table->string('product_id')->nullable();
            $table->string('name')->nullable();
            $table->string('qty')->nullable();
            $table->string('unit')->nullable();
            $table->float('price')->default(0);
            $table->float('total_percentage')->default(100);
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('order_products');
    }
}
