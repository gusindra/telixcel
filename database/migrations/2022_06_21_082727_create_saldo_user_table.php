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
        Schema::create('saldo_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('team_id', 50)->nullable();
            $table->enum('mutation', ['debit', 'credit']);
            $table->string('description', 100);
            $table->string('currency')->comment('for amount');
            $table->float('amount', 12, 2);
            $table->float('balance', 12, 2)->comment('in idr');
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
        Schema::dropIfExists('saldo_users');
    }
};
