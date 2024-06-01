<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\AuthDTO;
use App\Interface\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function __construct(private  User $user)
    {

    }
    public function createUser(AuthDTO $data, string $passwordHash): User
    {
        return $this->user->create([
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => $passwordHash,
        ]);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }
}
