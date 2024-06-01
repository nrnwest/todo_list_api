<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\FilterDTO;
use App\DTO\TaskDTO;
use App\DTO\TaskUpdateDTO;
use App\Enum\Status;
use App\Exceptions\BadRequestException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\TaskCompletedException;
use App\Http\Controllers\Controller;
use App\Http\Request\FilterRequest;
use App\Http\Request\TaskStoreRequest;
use App\Http\Request\TaskUpdateRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     operationId="filterTasks",
     *     tags={"Tasks"},
     *     summary="Filter and sort tasks using query parameters",
     *     description="Get tasks of the authenticated user with filters and sorting.",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Task status (e.g., 'todo', 'done')",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Task priority (1 to 5)",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="full text search by title and description",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="created_at",
     *         in="query",
     *         description="Order to sort by created_at ('asc' or 'desc')",
     *         required=false,
     *     ),
     *     @OA\Parameter(
     *         name="completed_at",
     *         in="query",
     *         description="Order to sort by completed_at ('asc' or 'desc')",
     *         required=false,
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="status", type="string", example="todo"),
     *                     @OA\Property(property="priority", type="integer", example=3),
     *                     @OA\Property(property="title", type="string", example="Project X"),
     *                     @OA\Property(property="description", type="string", example="Task details here"),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-05-04T10:00:00Z"
     *                     ),
     *                     @OA\Property(
     *                         property="completed_at",
     *                         type="string",
     *                         format="date-time",
     *                         nullable=true,
     *                         example=null
     *                     ),
     *                     @OA\Property(property="subtasks", type="array", @OA\Items(type="object"))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(FilterRequest $request): JsonResponse
    {
        return response()->json(
            $this->taskService->getUserTasks(
                new FilterDTO(
                    Auth::id(),
                    $request->get('status'),
                    $request->get('priority'),
                    $request->get('search'),
                    $request->get('created_at'),
                    $request->get('completed_at')
                )
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{id}",
     *     operationId="showTask",
     *     tags={"Tasks"},
     *     summary="Retrieve a specific task by ID",
     *     description="Fetch the details of a specific task using its unique identifier.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the task to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="todo"),
     *                 @OA\Property(property="priority", type="integer", example=3),
     *                 @OA\Property(property="title", type="string", example="Finish project X"),
     *                 @OA\Property(property="description", type="string", example="Complete all tasks related to
     *                                                      project X."),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-05-04T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="completed_at",
     *                     type="string",
     *                     format="date-time",
     *                     nullable=true,
     *                     example=null
     *                 ),
     *                 @OA\Property(property="subtasks", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     )
     * )
     */
    public function show(Task $task): JsonResponse
    {
        if (Gate::denies('view', $task)) {
            throw new ForbiddenException();
        }

        return response()->json($task);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     operationId="store",
     *     tags={"Tasks"},
     *     summary="Store a newly created resource in storage",
     *     description="Create a new task",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "priority", "status"},
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the task",
     *                 default="Title"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the task",
     *                 default="Description"
     *             ),
     *             @OA\Property(
     *                 property="parent_id",
     *                 type="integer",
     *                 nullable=true,
     *                 default=null,
     *                 description="Parent ID of the task"
     *             ),
     *             @OA\Property(
     *                 property="priority",
     *                 type="integer",
     *                 default=1,
     *                 description="Priority of the task on a scale of 1-5"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 default="todo",
     *                 description="Status of the task, either 'todo' or 'done'"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->taskService->store(
                new TaskDTO(
                    Auth::id(),
                    $request->get('title'),
                    $request->get('description'),
                    $request->get('priority'),
                    $request->enum('status', Status::class),
                    $request->get('parent_id')
                )
            ),
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/tasks/{id}/complete",
     *      operationId="completeTask",
     *      tags={"Tasks"},
     *      summary="Mark the specified task as complete",
     *      description="Update the completion status of a specific task",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="completed", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Task not found"
     *      )
     * )
     */
    public function complete(Task $task): JsonResponse
    {
        if (Gate::denies('update', $task)) {
            throw new ForbiddenException();
        }

        if ($task->status === Status::DONE->value) {
            throw new TaskCompletedException();
        }

        $this->taskService->taskComplete($task);

        return response()->json(['message' => 'Task marked as complete'], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/tasks/{id}",
     *      operationId="updateTask",
     *      tags={"Tasks"},
     *      summary="Update the specified resource in storage",
     *      description="Update a specific task",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", enum={"todo", "done"}, example="todo"),
     *              @OA\Property(property="priority", type="integer", minimum=1, maximum=5, example=3),
     *              @OA\Property(property="title", type="string", example="Finish documentation"),
     *              @OA\Property(property="description", type="string", example="Complete the documentation for task
     *                                                   management feature")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Task updated successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Task not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation error")
     *          )
     *      )
     * )
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        if (Gate::denies('update', $task)) {
            throw new ForbiddenException();
        }

        return response()->json(
            $this->taskService->update(
                new TaskUpdateDTO(
                    $request->get('title'),
                    $request->get('description'),
                    $request->get('priority'),
                    $request->enum('status', Status::class)
                ),
                $task
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/tasks/{id}",
     *      operationId="destroy",
     *      tags={"Tasks"},
     *      summary="Remove the specified resource from storage",
     *      description="Delete a specific task",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the task",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      ),
     * )
     */
    public function destroy(Task $task): JsonResponse
    {
        if (Gate::denies('delete', $task)) {
            throw new ForbiddenException();
        }

        if ($task->status === Status::DONE->value) {
            throw new TaskCompletedException();
        }

        $this->taskService->delete($task);

        return response()->json(['message' => 'Task deleted successfully.'], Response::HTTP_OK);
    }
}
