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
        Schema::create('flow_process', function (Blueprint $table) {
            $table->id();
            $table->string('model', 100)->nullable()->comment('base on ext: project or manual');
            $table->string('model_id', 50)->nullable()->comment('address to id');
            $table->foreignId('user_id');
            $table->string('status', 50)->nullable()->comment('address to id');
            $table->string('comment', 200)->nullable()->comment('address to id');
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
        Schema::dropIfExists('flow_process');
    }
};
