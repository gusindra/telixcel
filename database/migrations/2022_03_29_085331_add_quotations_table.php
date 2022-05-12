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
        Schema::create('quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('commerce_id');
            $table->string('source_id')->nullable();
            $table->string('model', 100)->nullable()->comment('base on ext: project or manual');
            $table->string('model_id', 50)->nullable()->comment('address to id');
            $table->text('terms');
            $table->float('discount');
            $table->float('price');
            $table->string('status', 100)->default('active')->comment('active, disabled');
            $table->foreignId('user_id');
            $table->timestamp('valid_until')->nullable();
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
        Schema::dropIfExists('quotations');
    }
};
