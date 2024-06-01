<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\Status;

class TaskDTO
{
    public function __construct(
        public int $user_id,
        public string $title,
        public string $description,
        public int $priority,
        public Status $status,
        public ?int $parent_id = null,
    ) {
    }
}
