<?php

declare(strict_types=1);

namespace App\Filter;

use App\DTO\FilterDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class CreatedAtFilter
{
    public function handle(Builder $builder, Closure $next, FilterDTO $data)
    {
        if ($data->created_at !== null) {
            $builder->orderBy('created_at', $data->created_at);
        }

        return $next($builder);
    }

}
