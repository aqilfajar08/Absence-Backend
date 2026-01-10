<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendence>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'date_attendance' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'time_in' => fake()->time('H:i:s', '09:00:00'),
            'time_out' => fake()->optional(0.8)->time('H:i:s', '18:00:00'),
            'latlon_in' => fake()->latitude() . ',' . fake()->longitude(),
            'latlon_out' => fake()->optional(0.8)->passthrough(fake()->latitude() . ',' . fake()->longitude()),
        ];
    }
}