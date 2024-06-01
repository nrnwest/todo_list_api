<?php

declare(strict_types=1);


namespace App\Interface;

use App\DTO\AuthDTO;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(AuthDTO $data, string $passwordHash): User;

    public function getUserByEmail(string $email): ?User;
}
