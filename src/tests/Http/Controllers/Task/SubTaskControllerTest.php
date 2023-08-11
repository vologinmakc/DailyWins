<?php

namespace Tests\Http\Controllers\Task;


use App\Constants\Response\ResponseStatuses;
use App\Models\Task\SubTask;
use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubTaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $user = User::factory()->create([
            'name'              => 'admin',
            'email'             => 'admin@admin.com',
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
        ]);
        $this->actingAs($user);

    }

    public function testCreateSubTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);
        $subTaskData = [
            'name'        => 'SubTask Name',
            'description' => 'SubTask Description',
            'task_id'     => $task->id
        ];

        $response = $this->postJson('/api/subtasks', $subTaskData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'task_id',
                    'created_by',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonPath('data.name', $subTaskData['name'])
            ->assertJsonPath('data.description', $subTaskData['description'])
            ->assertJsonPath('data.task_id', $subTaskData['task_id'])
            ->assertJsonPath('data.created_by', $this->user->id);

        $this->assertDatabaseHas('sub_tasks', $subTaskData);
        $this->assertDatabaseHas('sub_task_snapshots', ['task_id' => $task->id]);
    }

    public function testReadSubTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);
        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        $response = $this->getJson('/api/subtasks/' . $subTask->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $subTask->name);

        $this->assertDatabaseHas('sub_tasks', ['id' => $subTask->id]);
    }

    public function testUpdateSubTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        $updatedData = ['name' => 'Updated Name'];

        $response = $this->putJson('/api/subtasks/' . $subTask->id, $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sub_tasks', array_merge(['id' => $subTask->id], $updatedData));
    }

    public function testDeleteSubTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        $response = $this->deleteJson('/api/subtasks/' . $subTask->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sub_tasks', ['id' => $subTask->id]);
        $this->assertDatabaseHas('sub_task_snapshots', ['task_id' => $task->id]);
    }

    public function testUnauthorizedAccessToSubTasks()
    {
        // Создаем подзадачу для первого пользователя
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);
        $subTask = SubTask::factory()->create([
            'task_id' => $task->id,
            'created_by' => $this->user->id
        ]);

        // Создаем второго пользователя
        $anotherUser = User::factory()->create();

        // Пытаемся получить доступ к подзадаче первого пользователя от имени второго пользователя
        $this->actingAs($anotherUser);

        $response = $this->getJson('/api/subtasks/' . $subTask->id);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);

        // Пытаемся обновить подзадачу первого пользователя от имени второго пользователя
        $response = $this->putJson('/api/subtasks/' . $subTask->id, ['name' => 'New Name']);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);

        // Пытаемся удалить подзадачу первого пользователя от имени второго пользователя
        $response = $this->deleteJson('/api/subtasks/' . $subTask->id);
        $response->assertJsonPath('result_code', ResponseStatuses::ERROR);
    }
}
