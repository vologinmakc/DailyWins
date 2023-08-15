<?php

namespace Tests\Http\Controllers\Task;


use App\Constants\Other\WeekDays;
use App\Constants\Response\ResponseStatuses;
use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\Task;
use App\Models\Task\TaskHistory;
use App\Models\Task\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        $this->user = $user = User::factory()->create([
            'name'              => 'admin',
            'email'             => 'admin@admin.com',
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
        ]);
        $this->accessToken = $this->user->createToken('test-token')->accessToken;

    }

    public function withHeaders(array $headers)
    {
        $headers['Authorization'] = 'Bearer ' . $this->accessToken;
        return parent::withHeaders($headers);
    }

    public function testCreateOneOffTask()
    {
        $payload = [
            'name'        => 'Одноразовая задача',
            'description' => 'Описание задачи',
            'start_date'  => Carbon::now()->format('Y-m-d'),
            'type'        => TaskType::TYPE_ONE_OFF,
            'subtasks'    => [
                [
                    'name'        => 'Подзадача 1',
                    'description' => 'Описание 1'
                ],
                [
                    'name'        => 'Подзадача 2',
                    'description' => 'Описание 2'
                ]
            ]
        ];

        $response = $this->withHeaders([])->postJson('/api/tasks', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('data.name', 'Одноразовая задача')
            ->assertJsonPath('data.description', 'Описание задачи')
            ->assertJsonPath('data.start_date', Carbon::now()->format('Y-m-d'))
            ->assertJsonPath('data.type', TaskType::TYPE_ONE_OFF)
            ->assertJsonPath('data.created_by', $this->user->id)
            ->assertJsonPath('data.status', TaskStatuses::TASK_IN_PROGRESS);

        $task = Task::where('name', 'Одноразовая задача')->first();
        $this->assertNotNull($task);
        $this->assertEquals(TaskType::TYPE_ONE_OFF, $task->type);
    }

    public function testCreateRecurringTaskWithoutEndDate()
    {
        $payload = [
            'name'        => 'Регулярная задача',
            'description' => 'Описание задачи',
            'start_date'  => Carbon::now()->format('Y-m-d'),
            'recurrence'  => [WeekDays::MONDAY, WeekDays::WEDNESDAY],
            'type'        => TaskType::TYPE_RECURRING,
            'subtasks'    => [
                [
                    'name'        => 'Подзадача 1',
                    'description' => 'Описание 1'
                ],
                [
                    'name'        => 'Подзадача 2',
                    'description' => 'Описание 2'
                ]
            ]
        ];

        $response = $this->withHeaders([])->postJson('/api/tasks', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('data.name', 'Регулярная задача')
            ->assertJsonPath('data.description', 'Описание задачи')
            ->assertJsonPath('data.start_date', Carbon::now()->format('Y-m-d'))
            ->assertJsonPath('data.recurrence', [WeekDays::MONDAY, WeekDays::WEDNESDAY])
            ->assertJsonPath('data.type', TaskType::TYPE_RECURRING)
            ->assertJsonPath('data.created_by', $this->user->id);

        $task = Task::where('name', 'Регулярная задача')->first();
        $this->assertNotNull($task);
        $this->assertEquals(TaskType::TYPE_RECURRING, $task->type);

        // Проверяем, что в таблице истории изменений создана запись после создания задачи
        $this->assertDatabaseHas('task_histories', [
            'task_id'       => $task->id,
            'recurrence' => json_encode([WeekDays::MONDAY, WeekDays::WEDNESDAY]),
        ]);
    }

    public function testUpdateTaskNameAndDescription()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_ONE_OFF
        ]);

        $payload = [
            'name'        => 'Обновленное название',
            'description' => 'Обновленное описание'
        ];

        $response = $this->withHeaders([])->putJson('/api/tasks/' . $task->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('data.name', 'Обновленное название')
            ->assertJsonPath('data.description', 'Обновленное описание');

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'name'        => 'Обновленное название',
            'description' => 'Обновленное описание'
        ]);
    }

    public function testChangeTaskType()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_ONE_OFF
        ]);

        $payload = [
            'type'       => TaskType::TYPE_RECURRING,
            'recurrence' => [WeekDays::MONDAY, WeekDays::WEDNESDAY]
        ];

        $response = $this->withHeaders([])->putJson('/api/tasks/' . $task->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('data.type', TaskType::TYPE_RECURRING);

        $this->assertDatabaseHas('tasks', [
            'id'   => $task->id,
            'type' => TaskType::TYPE_RECURRING
        ]);
    }

    public function testChangeTaskRecurrence()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING,
            'recurrence' => json_encode([WeekDays::MONDAY, WeekDays::WEDNESDAY])
        ]);

        $payload = ['recurrence' => [WeekDays::TUESDAY, WeekDays::THURSDAY]];

        $response = $this->withHeaders([])->putJson('/api/tasks/' . $task->id, $payload);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE)
            ->assertJsonPath('data.recurrence', [WeekDays::TUESDAY, WeekDays::THURSDAY]);

        $this->assertDatabaseHas('tasks', [
            'id'         => $task->id,
            'recurrence' => json_encode([WeekDays::TUESDAY, WeekDays::THURSDAY])
        ]);

        // Проверяем, что в таблице истории изменений создана запись после обновления задачи
        $this->assertDatabaseHas('task_histories', [
            'task_id'       => $task->id,
            'recurrence' => json_encode([WeekDays::TUESDAY, WeekDays::THURSDAY]),
        ]);
    }

    public function testDeleteTask()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id
        ]);

        $response = $this->withHeaders([])->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonPath('result_code', ResponseStatuses::COMPLETE);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testFilterTasksForToday()
    {
        $today = Carbon::now()->format('Y-m-d');

        Task::factory()->create([
            'created_by' => $this->user->id,
            'start_date' => $today,
            'type'       => TaskType::TYPE_ONE_OFF
        ]);

        $response = $this->withHeaders([])->getJson('/api/tasks?search[start_date_or_day]=' . $today);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.start_date', $today);
    }

    public function testFilterTasksForSpecificDate()
    {
        $specificDate = Carbon::now()->addDays(2)->format('Y-m-d');

        Task::factory()->create([
            'created_by' => $this->user->id,
            'start_date' => $specificDate,
            'type'       => TaskType::TYPE_ONE_OFF
        ]);

        $response = $this->withHeaders([])->getJson('/api/tasks?search[start_date_or_day]=' . $specificDate);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.start_date', $specificDate);
    }

    public function testFilterByStartDateOrDayOfWeek()
    {
        // Определение текущего дня недели
        $today = Carbon::now()->format('Y-m-d');
        $currentDayOfWeek = Carbon::now()->dayOfWeekIso; // Возвращает от 1 (понедельник) до 7 (воскресенье)

        // Создаем задачи
        $task1 = Task::factory()->create(['start_date' => $today, 'created_by' => $this->user->id]);
        $task2 = Task::factory()->create(['recurrence' => [$currentDayOfWeek], 'created_by' => $this->user->id]);

        // Фильтр по текущей дате
        $response = $this->withHeaders([])->getJson('/api/tasks?search[start_date_or_day]=' . $today);
        $response->assertStatus(200);
        $this->assertContains($task1->id, $response->json('data.*.id'));
        $this->assertContains($task2->id, $response->json('data.*.id'));

        // Фильтр по текущему дню недели
        $response = $this->withHeaders([])->getJson('/api/tasks?search[start_date_or_day]=' . $currentDayOfWeek);
        $response->assertStatus(200);
        $this->assertContains($task2->id, $response->json('data.*.id'));
        $this->assertNotContains($task1->id, $response->json('data.*.id'));
    }

    public function testFilterByTaskType()
    {
        // Создаем задачи
        $oneOffTask = Task::factory()->create(['type' => TaskType::TYPE_ONE_OFF, 'created_by' => $this->user->id]);
        $recurringTask = Task::factory()->create(['type' => TaskType::TYPE_RECURRING, 'created_by' => $this->user->id]);

        // Фильтр по типу одноразовой задачи
        $response = $this->withHeaders([])->getJson('/api/tasks?search[type]=' . TaskType::TYPE_ONE_OFF);
        $response->assertStatus(200);
        $this->assertContains($oneOffTask->id, $response->json('data.*.id'));
        $this->assertNotContains($recurringTask->id, $response->json('data.*.id'));

        // Фильтр по типу регулярной задачи
        $response = $this->withHeaders([])->getJson('/api/tasks?search[type]=' . TaskType::TYPE_RECURRING);
        $response->assertStatus(200);
        $this->assertContains($recurringTask->id, $response->json('data.*.id'));
        $this->assertNotContains($oneOffTask->id, $response->json('data.*.id'));
    }

    public function testFilterByDifferentDateButMatchingDayOfWeek()
    {
        // Определение даты, отличной от текущей
        $differentDate = Carbon::now()->addDays(3)->format('Y-m-d');
        $dayOfWeekForDifferentDate = Carbon::parse($differentDate)->dayOfWeekIso;

        // Создаем периодическую задачу для дня недели отличной даты
        $task = Task::factory()->create(['recurrence' => [$dayOfWeekForDifferentDate], 'created_by' => $this->user->id]);

        // Фильтр по дню недели отличной даты
        $response = $this->withHeaders([])->getJson('/api/tasks?search[start_date_or_day]=' . $differentDate);
        $response->assertStatus(200);
        $this->assertContains($task->id, $response->json('data.*.id'));
    }

    public function testPeriodicTaskForCurrentDay()
    {
        // Определение текущего дня недели
        $currentDayOfWeek = Carbon::now()->dayOfWeekIso; // Возвращает от 1 (понедельник) до 7 (воскресенье)

        // Создаем периодическую задачу для текущего дня недели
        $task = Task::factory()->create([
            'recurrence' => [$currentDayOfWeek],
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING
        ]);

        // Получаем эту задачу для текущего дня
        $response = $this->withHeaders([])->getJson('/api/tasks/' . $task->id);

        // Проверяем, что задача доступна и ее статус на текущий день совпадает с ожидаемым
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.status', TaskStatuses::TASK_IN_PROGRESS);
    }

    public function testPeriodicTaskCompletedForCurrentDay()
    {
        // Определение текущего дня недели и текущей даты
        $currentDayOfWeek = Carbon::now()->dayOfWeekIso; // Возвращает от 1 (понедельник) до 7 (воскресенье)
        $today = Carbon::now()->format('Y-m-d');

        // Создаем периодическую задачу для текущего дня недели
        $task = Task::factory()->create([
            'recurrence' => [$currentDayOfWeek],
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING
        ]);

        // Добавляем статус завершенности для задачи на текущий день
        TaskStatus::create([
            'task_id' => $task->id,
            'date'    => $today,
            'status'  => TaskStatuses::TASK_COMPLETED
        ]);

        // Получаем эту задачу для текущего дня
        $response = $this->withHeaders([])->getJson('/api/tasks/' . $task->id);

        // Проверяем, что задача доступна и ее статус на текущий день совпадает с ожидаемым
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.status', TaskStatuses::TASK_COMPLETED);
    }


    public function testPeriodicTaskForSpecificDay()
    {
        // Определение дня недели, который отличается от текущего
        $specificDayOfWeek = (Carbon::now()->dayOfWeek + 1) % 7; // Если сегодня воскресенье, то выбираем понедельник

        // Создаем периодическую задачу для выбранного дня недели
        $task = Task::factory()->create([
            'recurrence' => [$specificDayOfWeek],
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING
        ]);

        // Получаем эту задачу для выбранного дня
        $specificDate = Carbon::now()->next($specificDayOfWeek)->format('Y-m-d');
        $response = $this->withHeaders([])->getJson('/api/tasks/' . $task->id . '?date=' . $specificDate);

        // Проверяем, что задача доступна и ее статус на выбранную дату совпадает с ожидаемым
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.status', TaskStatuses::TASK_IN_PROGRESS);
    }

    public function testTaskHistoryWithRealScenario()
    {
        // Шаг 1: Создаем периодическую задачу для первых 2 недель (по вторникам)
        $initialRecurrence = [WeekDays::TUESDAY];
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'type'       => TaskType::TYPE_RECURRING,
            'recurrence' => json_encode($initialRecurrence)
        ]);
        TaskHistory::create([
            'task_id'    => $task->id,
            'recurrence' => $initialRecurrence,
            'changed_at' => Carbon::now()
        ]);

        // Мимикрируем прохождение 2 недель
        Carbon::setTestNow(Carbon::now()->addWeeks(2));

        // Обновляем задачу для следующих 4 недель
        $updatedRecurrence = [WeekDays::WEDNESDAY, WeekDays::THURSDAY];
        $this->withHeaders([])->putJson('/api/tasks/' . $task->id, ['recurrence' => $updatedRecurrence]);

        // Мимикрируем прохождение 4 недель
        Carbon::setTestNow(Carbon::now()->addWeeks(4));

        // Отмечаем задачу как выполненную только для первого дня
        TaskStatus::create([
            'task_id' => $task->id,
            'date'    => Carbon::now()->subWeeks(6)->next(WeekDays::TUESDAY),
            'status'  => TaskStatuses::TASK_COMPLETED
        ]);

        // Получаем историю задачи
        $response = $this->withHeaders([])->getJson('/api/tasks/' . $task->id . '?expand=history');

        // Проверяем ответ
        $response->assertStatus(200)
            ->assertJsonCount(10, 'data.history');

        /*$startDate = Carbon::now()->subWeeks(6);
        $history = Arr::get($response->json(), 'data.history');
        for ($i = 0; $i < 10; $i++) {
            if ($i < 2) {
                // Первые 2 вторника
                $expectedDate = $startDate->next(WeekDays::TUESDAY)->format('Y-m-d');
                $status = $i == 0 ? TaskStatuses::TASK_COMPLETED : TaskStatuses::TASK_IN_PROGRESS;
                $this->assertEquals($expectedDate, $history[$i]['date']);
                $this->assertEquals($status, $history[$i]['status']);
            } else {
                // Следующие 4 среды и 4 пятницы
                // за первым заходом остаток будет ноль поэтому будет среда и т д
                $day = $i % 2 == 0 ? WeekDays::WEDNESDAY : WeekDays::THURSDAY;
                $expectedDate = $startDate->next($day)->format('Y-m-d');
                $this->assertEquals($expectedDate, $history[$i]['date']);
            }
        }*/
    }
}
