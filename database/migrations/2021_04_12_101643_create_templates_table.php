<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('description');
            $table->string('trigger_condition')->default('equals');
            $table->string('trigger')->nullable();
            $table->smallInteger('order')->default(1);
            $table->foreignId('error_template_id')->nullable();
            $table->foreignId('is_repeat_if_error')->nullable();
            $table->foreignId('template_id')->nullable();
            $table->smallInteger('is_enabled')->default(1);
            $table->foreignId('user_id');
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
        Schema::dropIfExists('templates');
    }
}
