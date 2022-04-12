<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->nullable();
            $table->text('reply')->comment("the message send/retrive");;
            $table->string('media')->nullable();
            $table->string('client_id')->comment("client id / room chat");
            $table->string('from')->comment("sender");
            $table->string('identity')->nullable();
            $table->foreignId('context_id')->nullable();
            $table->foreignId('template_id')->nullable();
            $table->foreignId('team_id');
            $table->foreignId('user_id')->comment("owner");;
            $table->string('type');
            $table->smallInteger('is_read')->default(0);
            $table->timestamp('sent_at')->nullable();
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
        Schema::dropIfExists('requests');
    }
}
