<?php

declare(strict_types=1);

namespace App\DTO;

class FilterDTO
{
    public function __construct(
        public int $user_id,
        public ?string $status,
        public ?int $priority,
        public ?string $search,
        public ?string $created_at,
        public ?string $completed_at
    ) {
    }
}
