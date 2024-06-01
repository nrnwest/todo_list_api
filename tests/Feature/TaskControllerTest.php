<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enum\Order;
use App\Enum\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanCreate_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'title'       => 'New Task',
            'description' => 'Description for the new task',
            'priority'    => 1,
            'status'      => Status::TODO->value,
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonStructure(['id', 'title', 'description', 'priority', 'status']);

        $this->assertDatabaseHas('tasks', [
            'title'       => 'New Task',
            'description' => 'Description for the new task',
            'priority'    => 1,
            'status'      => Status::TODO->value,
            'user_id'     => $user->id,
        ]);
    }

    public function testCanListTasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get('/api/v1/tasks');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([]);
    }

    public function testCanFilterOrderListTasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Task::factory()->count(10)->create(['user_id' => $user->id]);

        $dataFiltersOne = [
            'status'       => Status::TODO->value,
            'created_at'   => Order::DESC->value,
            'completed_at' => Order::ASC->value,
        ];
        $response       = $this->get('/api/v1/tasks?' . http_build_query($dataFiltersOne));
        $dataOne        = $response->json([]);

        $dataFiltersTwo = [
            'status'       => Status::DONE->value,
            'created_at'   => Order::ASC->value,
            'completed_at' => Order::DESC->value,
        ];
        $response       = $this->get('/api/v1/tasks?' . http_build_query($dataFiltersTwo));
        $dataTwo        = $response->json([]);

        $this->assertNotEquals($dataOne, $dataTwo);
    }

    public function testCanShowTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id]);
        $response = $this->get('/api/v1/tasks/' . $task->id);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCanCheckThatTaskDoesNotExist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/v1/tasks/' . 10000348);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUserCannotSeeOthersTasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'title'       => 'New Task2',
            'description' => 'Description for the new task2',
            'priority'    => 1,
            'status'      => Status::TODO->value,
        ];

        $response = $this->postJson('/api/v1/tasks', $taskData);
        $idTask = $response->json('id');

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $response = $this->get('/api/v1/tasks/' . $idTask);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanUpdateTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'title'       => 'Updated Task',
            'description' => 'Updated description',
            'priority'    => 5,
            'status'      => Status::DONE->value,
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure(['id', 'title', 'description', 'priority', 'status']);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task', 'id' => $task->id]);
    }

    public function testCanDeleteTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // delete task
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => Status::TODO->value]);

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");
        $response->assertStatus(Response::HTTP_OK);

        // not delete task
        $task = Task::factory()->create(['user_id' => $user->id, 'status' => Status::DONE->value]);

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCanCompleteTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id, 'status' => Status::TODO->value]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/complete");

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => Status::DONE->value]);
    }

    public function testNotDeleteOtherUserTask()
    {
        $userOther = User::factory()->create();
        $this->actingAs($userOther);
        $task = Task::factory()->create(['user_id' => $userOther->id, 'status' => Status::TODO->value]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotOtherUserCanCompleteTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id, 'status' => Status::TODO->value]);

        $userOther = User::factory()->create();
        $this->actingAs($userOther);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}/complete");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotOtherUserCanUpdateTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $userOther = User::factory()->create();
        $this->actingAs($userOther);

        $updateData = [
            'title'       => 'Updated Task',
            'description' => 'Updated description',
            'priority'    => 5,
            'status'      => Status::DONE->value,
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $updateData);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

}
