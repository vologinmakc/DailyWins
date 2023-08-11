<?php

namespace Database\Factories\Task;

use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'name'       => $this->faker->name,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'type'       => TaskType::TYPE_ONE_OFF
        ];
    }
}
