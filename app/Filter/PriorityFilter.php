<?php

declare(strict_types=1);

namespace App\Filter;

use App\DTO\FilterDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class PriorityFilter
{
    public function handle(Builder $builder, Closure $next, FilterDTO $data)
    {
        if ($data->priority !== null) {
            $builder->where('priority', $data->priority);
        }

        return $next($builder);
    }

}
