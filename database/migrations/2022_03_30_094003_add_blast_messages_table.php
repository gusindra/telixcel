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
        Schema::create('blast_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('msg_id', 50);
            $table->foreignId('user_id');
            $table->string('client_id', 50)->comment("client id");
            $table->string('type', 50);
            $table->string('status', 50)->nullable()->comment('DELIVERED, SENDING');
            $table->string('message_content', 185);
            $table->string('balance', 100);
            $table->string('msisdn', 100);
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
        Schema::dropIfExists('blast_messages');
    }
};
