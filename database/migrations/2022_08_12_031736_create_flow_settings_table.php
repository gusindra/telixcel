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
        Schema::create('flow_settings', function (Blueprint $table) {
            $table->id();
            $table->string('model', 100);
            $table->string('after_status', 100);
            $table->string('description', 100);
            $table->foreignId('role_id', 100);
            $table->foreignId('user_id', 100)->nullable()->comment('spesific executor');;
            $table->foreignId('team_id');
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
        Schema::dropIfExists('flow_settings');
    }
};
