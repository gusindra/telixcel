<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->nullable()->comment('Selling Product, SAAS Service, Referral');
            $table->string('entity_party')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('contact_id')->nullable();
            $table->string('referrer_id')->nullable();
            $table->string('referrer_name')->nullable();
            $table->string('commision_ratio')->nullable();
            $table->string('product_line')->nullable();
            $table->string('status', 100)->default('draft');
            $table->foreignId('team_id')->nullable();
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
        // Schema::dropIfExists('projects');
    }
}
