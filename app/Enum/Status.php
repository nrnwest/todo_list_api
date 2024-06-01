<?php

declare(strict_types=1);

namespace App\Enum;

enum Status: string
{
    case TODO = 'todo';
    case DONE = 'done';
}
