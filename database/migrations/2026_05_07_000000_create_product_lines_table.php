<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLinesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('product_lines')) {
            Schema::create('product_lines', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_lines');
    }
}
