<?php

namespace Database\Factories;

use App\Models\RequestType;
use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Saisine>
 */
class SaisineFactory extends Factory
{
    protected $model = Saisine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => 'SAI-' . fake()->unique()->numberBetween(1000, 9999),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'request_type_id' => RequestType::factory(),
            'creator_id' => User::factory(),
            'current_stage_id' => WorkflowStage::factory(),
            'urgency_level' => fake()->randomElement(['critical', 'priority', 'standard']),
            'custom_fields' => [],
            'stage_entered_at' => now(),
        ];
    }
}
