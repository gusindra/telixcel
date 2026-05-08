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
            $table->string('type')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('quote_no')->nullable();
            $table->string('commerce_id')->nullable();
            $table->string('source_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('model', 100)->nullable()->comment('base on ext: project or manual');
            $table->string('model_id', 50)->nullable()->comment('address to id');
            $table->text('terms')->nullable();
            $table->float('discount')->default(0);
            $table->float('price')->default(0);
            $table->string('status', 100)->default('draft')->comment('draft, active, disabled, approved, submit');
            $table->integer('valid_day')->default(0);
            $table->date('date')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_role')->nullable();
            $table->string('addressed_name')->nullable();
            $table->string('addressed_role')->nullable();
            $table->string('addressed_company')->nullable();
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
