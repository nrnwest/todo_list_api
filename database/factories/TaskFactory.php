<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enum\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement([Status::TODO->value, Status::DONE->value]),
            'priority' => $this->faker->randomElement([1,2,3,4,5]),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'created_at' => now(),
            'completed_at' => $this->faker->randomElement([now(), null]),
            'user_id' => User::factory(),
            'parent_id' => null,
        ];
    }
}
