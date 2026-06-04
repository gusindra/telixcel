<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_client')) {
            return;
        }

        Schema::create('project_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->index();
            $table->foreignId('client_id')->index(); // references clients.id
            $table->timestamps();
            $table->unique(['project_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_client');
    }
};
