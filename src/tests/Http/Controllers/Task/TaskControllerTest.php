<?php

namespace Tests\Http\Controllers\Task;


use App\Constants\Response\ResponseStatuses;
use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);
        $this->actingAs($user);

    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    public function testCreateTaskWithSubTasks()
    {
        $payload = [
            'name' => 'Основная задача',
            'status' => 1,
            'subtasks' => [
                [
                    'name' => 'Подзадача 1',
                    'description' => 'Описание 1',
                    'status' => 1
                ],
                [
                    'name' => 'Подзадача 2',
                    'description' => 'Описание 2',
                    'status' => 2
                ]
            ]
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('result_message', ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE])
            ->assertJsonPath('data.name', 'Основная задача')
            ->assertJsonPath('data.status', 1)
            ->assertJsonPath('data.created_by', $this->user->id);

        $task = Task::where('name', 'Основная задача')->first();
        $this->assertNotNull($task);

        foreach ($payload['subtasks'] as $subtaskPayload) {
            $this->assertDatabaseHas('sub_tasks', array_merge($subtaskPayload, ['task_id' => $task->id]));
        }
    }


    public function testReadTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('result_message', ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE]);
    }

    public function testUpdateTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $payload = [
            'name' => 'Updated Task',
            'status' => 2
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('result_message', ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE])
            ->assertJsonPath('data.name', 'Updated Task')
            ->assertJsonPath('data.status', 2)
            ->assertJsonPath('data.created_by', $this->user->id);

        $this->assertDatabaseHas('tasks', $payload);
    }

    public function testDeleteTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('result_message', ResponseStatuses::MESSAGES[ResponseStatuses::COMPLETE]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testUnauthorizedAccessToTasks()
    {
        // Создаем задачу для первого пользователя
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        // Создаем второго пользователя
        $anotherUser = User::factory()->create();

        // Пытаемся получить доступ к задаче первого пользователя от имени второго пользователя
        $this->actingAs($anotherUser);

        $response = $this->getJson('/api/tasks/' . $task->id);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);

        // Пытаемся обновить задачу первого пользователя от имени второго пользователя
        $response = $this->putJson('/api/tasks/' . $task->id, ['name' => 'New Name']);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);

        // Пытаемся удалить задачу первого пользователя от имени второго пользователя
        $response = $this->deleteJson('/api/tasks/' . $task->id);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);
    }

}