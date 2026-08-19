<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ai_applications')) {
            Schema::create('ai_applications', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('api_key_hash', 64)->nullable()->unique();
                $table->string('api_key_prefix', 32)->nullable();
                $table->text('api_key_encrypted')->nullable();
                $table->string('status', 20)->default('active')->index();
                $table->unsignedInteger('rate_limit_per_minute')->nullable();
                $table->unsignedBigInteger('monthly_token_limit')->nullable();
                $table->decimal('monthly_cost_limit', 16, 4)->nullable();
                $table->string('cost_currency', 8)->default('USD');
                $table->boolean('require_end_user')->default(false);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('ai_applications') && ! Schema::hasColumn('ai_applications', 'api_key_encrypted')) {
            Schema::table('ai_applications', function (Blueprint $table) {
                $table->text('api_key_encrypted')->nullable()->after('api_key_prefix');
            });
        }

        if (! Schema::hasTable('ai_models')) {
            Schema::create('ai_models', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('model_identifier')->unique();
                $table->string('display_name');
                $table->string('provider', 50)->index();
                $table->boolean('enabled')->default(false)->index();
                $table->timestamp('verified_at')->nullable();
                $table->text('description')->nullable();
                $table->decimal('input_price_per_million', 16, 6)->nullable();
                $table->decimal('output_price_per_million', 16, 6)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('ai_models') && ! Schema::hasColumn('ai_models', 'verified_at')) {
            Schema::table('ai_models', function (Blueprint $table) {
                $table->timestamp('verified_at')->nullable()->after('enabled');
            });
        }

        if (! Schema::hasTable('ai_application_model')) {
            Schema::create('ai_application_model', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ai_application_id')->constrained('ai_applications')->cascadeOnDelete();
                $table->foreignId('ai_model_id')->constrained('ai_models')->cascadeOnDelete();
                $table->unique(['ai_application_id', 'ai_model_id'], 'ai_app_model_unique');
            });
        }

        if (! Schema::hasTable('ai_requests')) {
            Schema::create('ai_requests', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('request_id', 64)->unique();
                $table->foreignId('ai_application_id')->constrained('ai_applications')->cascadeOnDelete();
                $table->string('model');
                $table->boolean('stream')->default(false);
                $table->string('status', 20)->default('pending')->index();
                $table->unsignedSmallInteger('http_status')->nullable();
                $table->unsignedInteger('latency_ms')->nullable();
                $table->string('error_code', 64)->nullable();
                $table->string('error_message', 500)->nullable();
                $table->string('end_user_id', 191)->nullable();
                $table->string('end_user_name', 191)->nullable();
                $table->string('end_user_email', 191)->nullable();
                $table->string('feature', 191)->nullable();
                $table->string('session_id', 191)->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['ai_application_id', 'created_at'], 'ai_requests_app_created_idx');
                $table->index(['end_user_id', 'ai_application_id'], 'ai_requests_end_user_idx');
                $table->index('end_user_name');
                $table->index('end_user_email');
                $table->index('feature');
            });
        }

        if (! Schema::hasTable('ai_usage')) {
            Schema::create('ai_usage', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('ai_application_id')->constrained('ai_applications')->cascadeOnDelete();
                $table->foreignId('ai_request_id')->nullable()->constrained('ai_requests')->nullOnDelete();
                $table->string('model');
                $table->unsignedBigInteger('input_tokens')->nullable();
                $table->unsignedBigInteger('output_tokens')->nullable();
                $table->unsignedBigInteger('total_tokens')->nullable();
                $table->decimal('cost', 16, 8)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->string('end_user_id', 191)->nullable();
                $table->string('end_user_name', 191)->nullable();
                $table->string('feature', 191)->nullable();
                $table->timestamps();

                $table->index(['ai_application_id', 'created_at'], 'ai_usage_app_created_idx');
                $table->index(['end_user_id', 'ai_application_id'], 'ai_usage_end_user_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ai_usage');
        Schema::dropIfExists('ai_requests');
        Schema::dropIfExists('ai_application_model');
        Schema::dropIfExists('ai_models');
        Schema::dropIfExists('ai_applications');
    }
};
