<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\TaskDTO;
use App\Enum\Status;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private TaskRepository $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = new TaskRepository(new Task());
    }

    public function testGetTask(): void
    {
        $user    = User::factory()->create();
        $tdo     = new TaskDTO($user->id, 'New Task', 'Description for the new task', 1, Status::DONE);
        $taskNew = $this->taskRepository->add($tdo);
        $task    = $this->taskRepository->getTask($taskNew->id);

        $this->assertEquals($tdo->title, $task->title);
    }
}

