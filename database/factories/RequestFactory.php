<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'source_id'     =>  rand(1,5),
            'context'       =>  $this->faker->word(),
            'media'         =>  Str::random(10),
            'from'          =>  rand(1,5),
            'identity'      =>  Str::random(10),
            'user_id'       =>  User::factory(),
            'type'          =>  rand(1,5),
            'created_at'    => \Carbon\Carbon::now(),
            'updated_at'    => \Carbon\Carbon::now(),
        ];
    }
}
