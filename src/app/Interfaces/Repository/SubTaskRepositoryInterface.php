<?php

namespace App\Interfaces\Repository;

use App\Models\Task\SubTask;
use App\Services\Dto\Task\SubTaskDto;

interface SubTaskRepositoryInterface
{
    public function create(SubTaskDto $subTaskDto);
    public function update(SubTask $subTask, SubTaskDto $subTaskDto);
    public function delete(SubTask $subTask);
    public function find(int $id);
}
