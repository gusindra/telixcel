<?php

namespace Database\Seeders;

use App\Models\AiApplication;
use App\Models\AiModel;
use Illuminate\Database\Seeder;

class AiGatewaySeeder extends Seeder
{
    public function run()
    {
        $claude = AiModel::updateOrCreate(
            ['model_identifier' => 'anthropic/claude-sonnet-4'],
            [
                'display_name' => 'Claude Sonnet 4',
                'provider' => 'anthropic',
                'enabled' => true,
                'verified_at' => now(),
                'description' => 'Claude Sonnet 4',
                'input_price_per_million' => 3,
                'output_price_per_million' => 15,
                'currency' => 'USD',
            ]
        );

        $gpt = AiModel::updateOrCreate(
            ['model_identifier' => 'openai/gpt-4o'],
            [
                'display_name' => 'GPT-4o',
                'provider' => 'openai',
                'enabled' => true,
                'verified_at' => now(),
                'description' => 'GPT-4o',
                'input_price_per_million' => 2.5,
                'output_price_per_million' => 10,
                'currency' => 'USD',
            ]
        );

        $gemini = AiModel::updateOrCreate(
            ['model_identifier' => 'google/gemini-2.0-flash'],
            [
                'display_name' => 'Gemini 2.0 Flash',
                'provider' => 'google',
                'enabled' => true,
                'verified_at' => now(),
                'description' => 'Gemini 2.0 Flash',
                'input_price_per_million' => 0.1,
                'output_price_per_million' => 0.4,
                'currency' => 'USD',
            ]
        );

        $isetalk = $this->seedApplication('iSetalk', 'isetalk', 60, 10000000, 100, [$claude->id, $gpt->id, $gemini->id]);
        $hireach = $this->seedApplication('Hireach', 'hireach', 100, 10000000, 100, [$gpt->id]);

        if ($this->command) {
            $this->command->info('AI Gateway seed complete.');
            $this->command->warn('iSetalk key (shown once): '.$isetalk);
            $this->command->warn('Hireach key (shown once): '.$hireach);
        }
    }

    private function seedApplication(string $name, string $slug, int $rpm, int $tokens, float $cost, array $modelIds): string
    {
        $app = AiApplication::firstOrNew(['slug' => $slug]);
        $app->fill([
            'name' => $name,
            'status' => AiApplication::STATUS_ACTIVE,
            'rate_limit_per_minute' => $rpm,
            'monthly_token_limit' => null,
            'monthly_cost_limit' => $cost,
            'cost_currency' => 'USD',
            'require_end_user' => true,
        ]);

        $raw = '';
        if (! $app->exists || ! $app->api_key_hash) {
            $raw = $app->issueKey();
        }
        $app->save();
        $app->models()->sync($modelIds);

        return $raw !== '' ? $raw : '(key already set; regenerate in admin)';
    }
}
