<?php

namespace Database\Seeders;

use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\SubTask;
use App\Models\Task\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    public function run()
    {
        $jsonData = '[
    {
        "name": "Разработка веб-приложения",
        "subTasks": [
            {
                "id": 1,
                "name": "Анализ требований",
                "description": "Изучение требований к веб-приложению от заказчика",
                "status": 2
            },
            {
                "id": 2,
                "name": "Проектирование базы данных",
                "description": "Создание структуры базы данных для веб-приложения",
                "status": 2
            },
            {
                "id": 3,
                "name": "Разработка пользовательского интерфейса",
                "description": "Создание интерактивного пользовательского интерфейса с использованием HTML, CSS и JavaScript",
                "status": 1
            },
            {
                "id": 4,
                "name": "Разработка серверной логики",
                "description": "Написание серверного кода для обработки запросов и взаимодействия с базой данных",
                "status": 1
            }
        ]
    },
    {
        "name": "Тестирование мобильного приложения",
        "subTasks": [
            {
                "id": 1,
                "name": "Планирование тестирования",
                "description": "Составление плана тестирования мобильного приложения",
                "status": 2
            },
            {
                "id": 2,
                "name": "Написание тестовых сценариев",
                "description": "Создание детальных сценариев для тестирования различных функциональных возможностей приложения",
                "status": 2
            },
            {
                "id": 3,
                "name": "Выполнение функционального тестирования",
                "description": "Проведение тестирования функциональности мобильного приложения",
                "status": 1
            },
            {
                "id": 4,
                "name": "Выполнение нагрузочного тестирования",
                "description": "Проверка производительности мобильного приложения при большой нагрузке",
                "status": 1
            }
        ]
    }
]';
        $data = json_decode($jsonData, true);

        $createdBy = User::find(1)->id;
        foreach ($data as $taskData) {
            $task = Task::create([
                'name'       => $taskData['name'],
                'type'       => TaskType::TYPE_ONE_OFF,
                'start_date' => Carbon::now(),
                'created_by' => $createdBy
            ]);

            foreach ($taskData['subTasks'] as $subTaskData) {
                SubTask::create([
                    'name'        => $subTaskData['name'],
                    'description' => $subTaskData['description'],
                    'task_id'     => $task->id,
                    'created_by'  => $createdBy,

                ]);
            }
        }
    }
}
