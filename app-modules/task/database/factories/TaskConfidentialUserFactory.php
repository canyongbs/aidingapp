<?php

namespace AidingApp\Task\Database\Factories;

use AidingApp\Task\Models\Task;
use AidingApp\Task\Models\TaskConfidentialUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskConfidentialUser>
 */
class TaskConfidentialUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
        ];
    }
}
