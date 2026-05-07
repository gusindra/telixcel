<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable()->comment('Selling Product, SAAS Service, Referral');
            $table->string('entity_party')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('referrer_id')->nullable();
            $table->string('commision_ratio')->nullable();
            $table->float('vat')->default(0);
            $table->float('total')->default(0);
            $table->string('status')->default('draft');
            $table->string('customer_id')->nullable();
            $table->string('source')->nullable();
            $table->string('source_id')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
