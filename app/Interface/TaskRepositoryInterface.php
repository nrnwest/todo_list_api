<?php

declare(strict_types=1);

namespace App\Interface;

use App\DTO\FilterDTO;
use App\DTO\TaskDTO;
use App\DTO\TaskUpdateDTO;
use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function add(TaskDTO $data): Task;

    public function getUserTasks(FilterDTO $data): Collection;

    public function taskComplete(Task $task): void;

    public function update(TaskUpdateDTO $data, Task $task): Task;

    public function delete(Task $task): bool;

    public function getTask(int $id): ?Task;
}
