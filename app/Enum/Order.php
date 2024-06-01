<?php

declare(strict_types=1);

namespace App\Enum;

enum Order: string
{
    case DESC = 'desc';
    case ASC = 'asc';
}
