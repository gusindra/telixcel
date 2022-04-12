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
        // Schema::create('projects', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->string('type')->comment('Selling Produc, SAAS Service, Referral');
        //     $table->string('entity_party');
        //     $table->string('customer_name');
        //     $table->string('customer_address');
        //     $table->string('customer_type');
        //     $table->string('contact_id');
        //     $table->string('referrer_id');
        //     $table->string('commision_ratio');
        //     $table->foreignId('user_id');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
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
