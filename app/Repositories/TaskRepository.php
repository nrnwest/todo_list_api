<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\FilterDTO;
use App\DTO\TaskDTO;
use App\DTO\TaskUpdateDTO;
use App\Enum\Status;
use App\Filter\CompletedAtFilter;
use App\Filter\CreatedAtFilter;
use App\Filter\PriorityFilter;
use App\Filter\SearchFilter;
use App\Filter\StatusFilter;
use App\Interface\TaskRepositoryInterface;
use App\Models\Task;
use App\Repositories\Trait\FilterTrait;
use Illuminate\Support\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    use FilterTrait;

    public function __construct(private Task $task)
    {

    }

    public function add(TaskDTO $data): Task
    {
        foreach ($data as $field => $value) {
            $this->task->$field = $value;
        }
        $this->task->save();

        return $this->task;
    }

    public function getUserTasks(FilterDTO $data): Collection
    {
        $filters = [
            StatusFilter::class,
            SearchFilter::class,
            PriorityFilter::class,
            CreatedAtFilter::class,
            CompletedAtFilter::class,
        ];

        $query = $this->task->where('user_id', $data->user_id);

        return $this->filter($query, $filters, $data)->with('subtasks')->get();
    }

    public function taskComplete(Task $task): void
    {
        $task->update(['completed_at' => now(), 'status' => Status::DONE->value]);
    }

    public function update(TaskUpdateDTO $data, Task $task): Task
    {
        foreach ($data as $field => $value) {
            $task->$field = $value;
        }
        $task->save();

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function getTask(int $id): ?Task
    {
        return $this->task->find($id);
    }
}
