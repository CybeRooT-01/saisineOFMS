<?php

namespace Database\Factories;

use App\Models\RequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestType>
 */
class RequestTypeFactory extends Factory
{
    protected $model = RequestType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'display_name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
