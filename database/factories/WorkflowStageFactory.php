<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\WorkflowStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkflowStage>
 */
class WorkflowStageFactory extends Factory
{
    protected $model = WorkflowStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'display_name' => fake()->words(3, true),
            'sequence_order' => fake()->numberBetween(1, 7),
            'expected_duration_days' => fake()->numberBetween(1, 10),
            'required_roles' => [Role::BUSINESS_TEAM],
        ];
    }
}
