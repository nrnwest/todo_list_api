<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test user registration.
     *
     * @return void
     */
    public function testUserRegistration()
    {
        // data registration
        $userData = [
            'name'                  => 'John Doe',
            'email'                 => 'johndoeTest23@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonStructure([
                     'token',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoeTest23@example.com',
        ]);

        $token = $response->json()['token'];

        $response = $this->withHeaders([
            'Authorization' => $token,
        ])->get('/api/v1/tasks/');

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function testUserLogin()
    {
        $user = User::firstOrCreate([
            'email' => 'demo@demo.com',
        ], [
            'name'              => 'Demo',
            'password'          => Hash::make('demo'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ]);

        $loginData = [
            'email'    => 'demo@demo.com',
            'password' => 'demo',
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'token',
                 ]);


        // check token
        $token = $response->json()['token'];
        $response = $this->withHeaders([
            'Authorization' => $token,
        ])->get('/api/v1/tasks/');

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test user login failure.
     *
     * @return void
     */
    public function testUserLoginFailure()
    {
        $loginData = [
            'email'    => 'nonexistent@example.com',
            'password' => 'invalidpassword',
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
                 ->assertJson([
                     'error' => 'Unauthorized',
                 ]);
    }
}
