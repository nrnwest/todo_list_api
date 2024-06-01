<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Facades\Hash;

class AuthDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $name = null
    ) {
    }
}
