<?php

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiModelFactory extends Factory
{
    protected $model = AiModel::class;

    public function definition()
    {
        return [
            'uuid' => (string) Str::uuid(),
            'model_identifier' => 'anthropic/claude-sonnet-4',
            'display_name' => 'Claude Sonnet 4',
            'provider' => 'anthropic',
            'enabled' => true,
            'verified_at' => now(),
            'description' => 'Claude Sonnet 4',
            'input_price_per_million' => 3,
            'output_price_per_million' => 15,
            'currency' => 'USD',
        ];
    }

    public function disabled()
    {
        return $this->state(['enabled' => false]);
    }
}
