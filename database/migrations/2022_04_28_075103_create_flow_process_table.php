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
        Schema::create('flow_processes', function (Blueprint $table) {
            $table->id();
            $table->string('model', 100)->nullable()->comment('base on ext: project or manual');
            $table->string('model_id', 50)->nullable()->comment('address to id');
            $table->foreignId('user_id');
            $table->string('status', 50)->nullable();
            $table->string('comment', 200)->nullable()->comment('remark');
            $table->string('task', 200)->nullable()->comment('task flow');
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
        Schema::dropIfExists('flow_processes');
    }
};
