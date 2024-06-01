<?php

declare(strict_types=1);

namespace App\Filter;

use App\DTO\FilterDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class CompletedAtFilter
{
    public function handle(Builder $builder, Closure $next, FilterDTO $data)
    {
        if ($data->completed_at !== null) {
            $builder->orderBy('completed_at', $data->completed_at);
        }

        return $next($builder);
    }

}
