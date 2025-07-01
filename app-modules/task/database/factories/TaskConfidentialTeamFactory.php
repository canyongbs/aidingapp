<?php

namespace AidingApp\Task\Database\Factories;

use AidingApp\Task\Models\Task;
use AidingApp\Task\Models\TaskConfidentialTeam;
use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskConfidentialTeam>
 */
class TaskConfidentialTeamFactory extends Factory
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
            'team_id' => Team::factory(),
        ];
    }
}
