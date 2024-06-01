<?php

declare(strict_types=1);

namespace App\Repositories\Trait;

use App\DTO\FilterDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Pipeline;

trait FilterTrait
{

    /**
     * @param   Builder    $builder
     * @param   array      $filters
     * @param   FilterDTO  $data  data from the request object
     *
     * @return Builder
     */
    public function filter(Builder $builder, array $filters, FilterDTO $data): Builder
    {
        return app(Pipeline::class)
            ->send($builder)
            ->through(
            // will return an array of anonymous functions that will be launched one by one
                array_map(function ($filter) use ($data) {
                    return function ($builder, $next) use ($filter, $data) {
                        return app($filter)->handle($builder, $next, $data);
                    };
                }, $filters)
            )
            ->thenReturn();
    }
}
