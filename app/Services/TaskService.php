<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\FilterDTO;
use App\DTO\TaskDTO;
use App\DTO\TaskUpdateDTO;
use App\Interface\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function __construct(private TaskRepositoryInterface $repository)
    {
    }

    public function store(TaskDTO $taskDTO): Task
    {
        return $this->repository->add($taskDTO);
    }

    public function getUserTasks(FilterDTO $filterDTO): Collection
    {
        return $this->repository->getUserTasks($filterDTO);
    }

    public function taskComplete(Task $task): void
    {
        $this->repository->taskComplete($task);
    }

    public function update(TaskUpdateDTO $data, Task $task): Task
    {
        return $this->repository->update($data, $task);
    }

    public function delete(Task $task): bool
    {
        return $this->repository->delete($task);
    }

}
