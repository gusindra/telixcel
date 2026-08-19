<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ai_settings')) {
            return;
        }

        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('base_url', 500)->nullable();
            $table->string('api_key', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_settings');
    }
};
