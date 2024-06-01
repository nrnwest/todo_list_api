<?php

declare(strict_types=1);

namespace App\Filter;

use App\DTO\FilterDTO;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    public function handle(Builder $builder, Closure $next, FilterDTO $data)
    {
        if ($data->search !== null) {
            $builder->whereFullText(['title', 'description'], $data->search);
        }

        return $next($builder);
    }

}
