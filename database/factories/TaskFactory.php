<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst($this->faker->words(3, true)),
            'description' => $this->faker->sentence(),
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(2)->toDateString(),
            'is_archived' => false,
        ];
    }

    public function done(): static
    {
        return $this->state(fn () => ['status' => 'done', 'completed_at' => now()]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['is_archived' => true]);
    }
}
