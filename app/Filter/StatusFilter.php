<?php

declare(strict_types=1);

namespace App\Filter;

use App\DTO\FilterDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class StatusFilter
{
    public function handle(Builder $builder, Closure $next, FilterDTO $data)
    {
        if ($data->status !== null) {
            $builder->where('status', $data->status);
        }

        return $next($builder);
    }

}
