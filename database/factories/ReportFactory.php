<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_number' => 'CF-'.fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'area_id' => Area::factory(),
            'category_id' => Category::factory(),
            'location_detail' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'urgency' => fake()->randomElement(['low', 'medium', 'high', 'emergency']),
            'status' => 'reported',
            'photo' => null,
            'assigned_to' => null,
            'reported_at' => now(),
        ];
    }
}
