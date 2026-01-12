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
        // Random time IN between 06:00 and 10:00
        $hour = fake()->numberBetween(6, 10);
        $minute = fake()->numberBetween(0, 59);
        $second = fake()->numberBetween(0, 59);
        
        $timeInStr = sprintf('%02d:%02d:%02d', $hour, $minute, $second);
        
        // Logika: Telat jika lewat jam 08:00:00 (Setting default Company)
        $isLate = ($hour > 8) || ($hour == 8 && ($minute > 0 || $second > 0));

        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? 1,
            'date_attendance' => fake()->dateTimeBetween('-45 days', 'now')->format('Y-m-d'),
            'time_in' => $timeInStr,
            'time_out' => fake()->optional(0.8)->time('H:i:s', '18:00:00'),
            'latlon_in' => fake()->latitude() . ',' . fake()->longitude(),
            'latlon_out' => fake()->optional(0.8)->passthrough(fake()->latitude() . ',' . fake()->longitude()),
            'is_late' => $isLate,
        ];
    }
}