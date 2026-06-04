<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tasks')) {
            return;
        }

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->index();
            $table->string('title');
            $table->string('type')->nullable();          // finance / admin / operasional (manual)
            $table->foreignId('owner_id')->nullable()->index();    // creator
            $table->foreignId('assigned_to')->nullable()->index(); // assignee
            $table->text('source')->nullable();          // client request remark (manual)
            $table->date('target_date')->nullable();     // default now+7 days (set in app)
            $table->string('status')->default('progress'); // progress / pending / complete
            $table->foreignId('team_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
