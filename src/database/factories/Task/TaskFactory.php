<?php

namespace Database\Factories\Task;

use App\Constants\Task\TaskStatuses;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'   => $this->faker->name,
            'status' => TaskStatuses::TASK_IN_PROGRESS
        ];
    }
}
