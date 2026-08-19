<?php

namespace Database\Factories;

use App\Models\AiApplication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AiApplicationFactory extends Factory
{
    protected $model = AiApplication::class;

    public function definition()
    {
        $raw = 'sk-'.bin2hex(random_bytes(24));

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->unique()->company,
            'slug' => $this->faker->unique()->slug(2),
            'api_key_hash' => hash('sha256', $raw),
            'api_key_prefix' => substr($raw, 0, 16),
            'api_key_encrypted' => encrypt($raw),
            'status' => AiApplication::STATUS_ACTIVE,
            'rate_limit_per_minute' => 60,
            'monthly_token_limit' => null,
            'monthly_cost_limit' => 100,
            'cost_currency' => 'USD',
            'require_end_user' => false,
        ];
    }

    public function inactive()
    {
        return $this->state(['status' => AiApplication::STATUS_INACTIVE]);
    }
}
