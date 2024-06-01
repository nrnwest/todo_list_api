<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\Status;
use App\Models\Task;

class TaskUpdateDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $priority,
        public Status $status
    ) {
    }
}
