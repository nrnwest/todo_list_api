<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\AuthDTO;
use App\Http\Request\UserStoreRequest;
use App\Http\Request\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     *      path="/api/v1/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="Register a new user",
     *      description="Creates a new user account with the provided name, email, and password.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="John Doe",
     *                      description="The name of the user."
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="john@example.com",
     *                      format="email",
     *                      description="The email address of the user."
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      example="password123",
     *                      description="The password for the user account."
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      example="password123",
     *                      description="The confirmation of the password."
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="token",
     *                  type="string",
     *                  example="token_string",
     *                  description="The access token for the newly registered user."
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Error message describing the validation failure."
     *              ),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  description="Object containing validation errors.",
     *                  @OA\AdditionalProperties(
     *                      type="array",
     *                      @OA\Items(type="string"),
     *                  ),
     *              ),
     *          ),
     *      ),
     * )
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = $this->authService->store(
            new AuthDTO(
                $request->get('email'),
                $request->get('password'),
                $request->get('name')
            )
        );

        return response()->json(['token' => $this->authService->formatToken($user, config('app.name'))], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Log in as an existing user",
     *      description="demo user in system",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="demo@demo.com",
     *                      format="email",
     *                      description="The email address of the user."
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      example="demo",
     *                      description="The password for the user account."
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="token",
     *                  type="string",
     *                  example="token_string",
     *                  description="The access token for the logged-in user."
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  description="Error message indicating authentication failure."
     *              ),
     *          ),
     *      ),
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authService->loginUser(
            new AuthDTO(
                $request->get('email'), $request->get('password')
            )
        );

        return response()->json(['token' => $this->authService->formatToken($user, config('app.name'))], Response::HTTP_OK);
    }
}
