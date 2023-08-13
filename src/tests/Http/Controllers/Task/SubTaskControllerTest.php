<?php

namespace Tests\Http\Controllers\Task;


use App\Constants\Other\WeekDays;
use App\Constants\Response\ResponseStatuses;
use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\SubTask;
use App\Models\Task\SubTaskSnapshot;
use App\Models\Task\SubTaskStatus;
use App\Models\Task\Task;
use App\Models\Task\TaskHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING
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

    public function testSoftDeleteSubTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        // Добавим статус для подзадачи
        SubTaskStatus::create([
            'sub_task_id' => $subTask->id,
            'date'        => now(),
            'status'      => TaskStatuses::TASK_COMPLETED
        ]);

        $response = $this->deleteJson('/api/subtasks/' . $subTask->id);
        $response->assertStatus(200);

        // Проверка на наличие мягко удаленной записи в базе данных
        $this->assertDatabaseHas('sub_tasks', ['id' => $subTask->id, 'deleted_at' => now()]);

        // Проверка отсутствия подзадачи при стандартном запросе через Eloquent
        $this->assertNull(SubTask::find($subTask->id));

        // Проверка наличия подзадачи при запросе с учетом мягко удаленных записей
        $this->assertNotNull(SubTask::withTrashed()->find($subTask->id));

        // Проверка сохранения статусов подзадачи
        $this->assertNotEmpty($subTask->getSubTaskStatusForDate());
    }

    public function testCascadeDeleteOnMainTaskDelete()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);
        $response->assertStatus(200);

        // Проверка на отсутствие основной задачи в базе данных
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);

        // Проверка на отсутствие подзадачи в базе данных
        $this->assertDatabaseMissing('sub_tasks', ['id' => $subTask->id]);

        // Проверка на отсутствие статусов для удаленной подзадачи
        $this->assertEmpty($subTask->getSubTaskStatusForDate());
    }

    public function testUnauthorizedAccessToSubTasks()
    {
        // Создаем подзадачу для первого пользователя
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);
        $subTask = SubTask::factory()->create([
            'task_id'    => $task->id,
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

    public function testUpdateSubTaskStatus()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING
        ]);

        $subTask = SubTask::factory()->create([
            'created_by' => $this->user->id,
            'task_id'    => $task->id
        ]);

        $newStatus = TaskStatuses::TASK_COMPLETED;

        $response = $this->postJson('/api/subtasks/' . $subTask->id . '/status', ['status' => $newStatus]);

        $response->assertStatus(200);

        // Проверяем, что подзадача существует
        $this->assertDatabaseHas('sub_tasks', ['id' => $subTask->id]);

        // Проверяем, что слепок был создан для задачи
        $this->assertDatabaseHas('sub_task_snapshots', ['task_id' => $task->id]);

        // Проверяем, что статус подзадачи был обновлен
        $this->assertDatabaseHas('sub_task_statuses', ['sub_task_id' => $subTask->id, 'status' => $newStatus]);
    }

    public function testTaskWithSubTaskHistory()
    {
        // 1. Создаем задачу с подзадачами
        $initialRecurrence = [WeekDays::TUESDAY];
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING,
            'recurrence' => json_encode($initialRecurrence)
        ]);
        $subTaskData = [
            'name'        => 'Удаленная',
            'description' => 'SubTask Description',
            'task_id'     => $task->id
        ];

        $response = $this->postJson('/api/subtasks', $subTaskData);
        $response->assertStatus(200);
        $subTaskId = $response->json()['data']['id'];

        // 2. Создаем первый слепок истории задачи
        TaskHistory::create([
            'task_id'    => $task->id,
            'recurrence' => $initialRecurrence,
            'changed_at' => Carbon::now()
        ]);

        // 3. Имитируем прохождение времени
        Carbon::setTestNow(Carbon::now()->addWeeks(3));

        // 4. Добавим новую подзадачу и удалим старую через api
        $subTaskData = [
            'name'        => 'Новая',
            'description' => 'SubTask Description',
            'task_id'     => $task->id
        ];

        $response = $this->postJson('/api/subtasks', $subTaskData);
        $response->assertStatus(200);
        $expectedCount = 2;
        $count = SubTaskSnapshot::where('task_id', $task->id)->count();
        $this->assertEquals($expectedCount, $count);

        $response = $this->deleteJson('/api/subtasks/' . $subTaskId, []);
        $response->assertStatus(200);
        $this->assertDatabaseHas('sub_task_snapshots', ['task_id' => $task->id]);
        $count = SubTaskSnapshot::where('task_id', $task->id)->count();
        $this->assertEquals($expectedCount, $count);

        Carbon::setTestNow(Carbon::now()->addWeeks(2));

        // 5. Делаем запрос на задачу с `expand=history`
        $response = $this->getJson('/api/tasks/' . $task->id . '?expand=history');

        // Проверяем ответ
        $response->assertStatus(200);

        // Проверка первых трех записей history
        for ($i = 0; $i < 3; $i++) {
            $response->assertJsonPath("data.history.$i.subtasks.0.name", "Удаленная");
        }

        // Проверка последних двух записей history
        for ($i = 3; $i < 5; $i++) {
            $response->assertJsonPath("data.history.$i.subtasks.0.name", "Новая");
        }
    }
}
