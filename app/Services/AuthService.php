<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\AuthDTO;
use App\Exceptions\UnauthorizedException;
use App\Interface\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;


class AuthService
{
    private const PRE_TOKEN = "Bearer\u{0020}";

    public function __construct(private AuthRepositoryInterface $authRepository, private Hasher $hasher)
    {
    }

    public function store(AuthDto $data): User
    {
        return $this->authRepository->createUser($data, $this->hasher->make($data->password));
    }

    public function loginUser(AuthDto $data): User
    {
        $user = $this->getUserByEmail($data->email);
        $this->checkPassword($data->password, $user->password);

        return $user;
    }

    public function getUserByEmail(string $email): User
    {
        $user = $this->authRepository->getUserByEmail($email);
        if (!$user) {
            throw new UnauthorizedException();
        }

        return $user;
    }

    public function checkPassword($password, $passwordHash): void
    {
        if ($this->hasher->check($password, $passwordHash)) {
            return;
        }

        throw new UnauthorizedException();
    }

    public function formatToken(User $user, string $appName): string
    {
        return self::PRE_TOKEN . $user->createToken($appName)->plainTextToken;
    }
}
