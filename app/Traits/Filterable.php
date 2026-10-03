<?php

namespace App\Traits;

use App\Filters\GlobalFilter;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilterGlobal(Builder $query, GlobalFilter $filter, array $searchableColumns = []): Builder
    {
        return $filter->apply($query, $searchableColumns);
    }
}
