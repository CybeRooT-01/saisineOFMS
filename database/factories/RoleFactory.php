<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                Role::BUSINESS_TEAM,
                Role::RJG,
                Role::COMPLIANCE_DIRECTOR,
                Role::DRJ,
                Role::INTERNAL_CONTRIBUTOR,
                Role::OBSERVER,
            ]),
            'display_name' => fake()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
