<?php

namespace Database\Factories\Task;

use App\Constants\Task\TaskStatuses;
use App\Models\Task\SubTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubTask>
 */
class SubTaskFactory extends Factory
{
    protected $model = SubTask::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->name,
            'description' => $this->faker->text('15')
        ];
    }
}
