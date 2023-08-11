<?php

namespace App\Services\Task\Handler;

use App\Constants\Task\TaskType;
use App\Interfaces\Task\TaskHandlerInterface;

class TaskHandlerFactory
{
    protected $handlers = [];

    public function __construct()
    {
        // Здесь мы регистрируем обработчики для разных типов задач.
        $this->handlers[TaskType::TYPE_ONE_OFF] = OneOffTaskHandler::class;
        $this->handlers[TaskType::TYPE_RECURRING] = RecurringTaskHandler::class;
    }

    public function make(string $type): TaskHandlerInterface
    {
        if (!isset($this->handlers[$type])) {
            throw new \InvalidArgumentException("Не известный тип задачи");
        }

        return app($this->handlers[$type]);
    }
}
