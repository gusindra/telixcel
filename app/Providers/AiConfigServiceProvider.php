<?php

namespace App\Providers;

use App\Models\AiSetting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AiConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    /**
     * Override the AI upstream endpoint from the Settings page when a value is stored.
     * Falls back to the .env values (AI_BASE_URL / AI_API_KEY / AI_ENDPOINT) otherwise.
     */
    public function boot()
    {
        if (Schema::hasTable('ai_models') && ! Schema::hasColumn('ai_models', 'verified_at')) {
            Schema::table('ai_models', function (Blueprint $table) {
                $table->timestamp('verified_at')->nullable()->after('enabled');
            });
        }

        if (! Schema::hasTable('ai_settings')) {
            return;
        }

        try {
            $setting = AiSetting::stored();
        } catch (\Throwable $e) {
            return;
        }

        $base = trim((string) ($setting->base_url ?? ''));
        if ($base === '') {
            return;
        }

        $base = rtrim($base, '/');
        config(['ai.base_url' => $base]);
        config(['ai.endpoint' => $base.'/chat/completions']);

        $key = trim((string) ($setting->api_key ?? ''));
        if ($key !== '') {
            config(['ai.api_key' => $key]);
        }
    }
}
