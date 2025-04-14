<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 10),
            'date_permission' => fake()->date(),
            'reason' => fake()->sentence(),
            // enum pending reject approve with default pending
            'is_approved' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
