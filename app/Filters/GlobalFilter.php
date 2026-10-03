<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GlobalFilter
{
    protected Request $request;

    protected Builder $builder;

    // Parameter URL bawaan yang diabaikan saat filtering
    protected array $ignoredParams = ['page', 'per_page', 'sort_by', 'sort_direction', 'search'];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder, array $searchableColumns = []): Builder
    {
        $this->builder = $builder;

        // 1. Eksekusi Global Search jika parameter 'search' dikirimkan
        if ($this->request->filled('search') && ! empty($searchableColumns)) {
            $this->applySearch($this->request->query('search'), $searchableColumns);
        }

        // 2. Eksekusi filter eksak & relasi dari sisa query parameter
        foreach ($this->request->query() as $key => $value) {
            if (in_array($key, $this->ignoredParams, true) || $value === null || $value === '') {
                continue;
            }

            $this->applyFieldFilter($key, $value);
        }

        // 3. Eksekusi pengurutan (Sorting) opsional
        if ($this->request->filled('sort_by')) {
            $direction = $this->request->query('sort_direction', 'asc');
            $this->builder->orderBy($this->request->query('sort_by'), $direction);
        }

        return $this->builder;
    }

    /**
     * Memproses filter kolom langsung atau relasi (misal: 'siswa__kelas_id' atau 'siswa.kelas_id')
     */
    protected function applyFieldFilter(string $field, mixed $value): void
    {
        $delimiter = str_contains($field, '__') ? '__' : (str_contains($field, '.') ? '.' : null);

        if ($delimiter) {
            $segments = explode($delimiter, $field);
            $targetColumn = array_pop($segments);
            $relationPath = implode('.', $segments);

            $this->builder->whereHas($relationPath, function (Builder $query) use ($targetColumn, $value) {
                is_array($value)
                    ? $query->whereIn($targetColumn, $value)
                    : $query->where($targetColumn, $value);
            });

            return;
        }

        is_array($value)
            ? $this->builder->whereIn($field, $value)
            : $this->builder->where($field, $value);
    }

    /**
     * Memproses pencarian LIKE multi-kolom dan multi-relasi
     */
    protected function applySearch(string $keyword, array $columns): void
    {
        $this->builder->where(function (Builder $masterQuery) use ($keyword, $columns) {
            foreach ($columns as $column) {
                if (str_contains($column, '.')) {
                    $segments = explode('.', $column);
                    $targetColumn = array_pop($segments);
                    $relationPath = implode('.', $segments);

                    $masterQuery->orWhereHas($relationPath, function (Builder $relQuery) use ($targetColumn, $keyword) {
                        $relQuery->where($targetColumn, 'like', "%{$keyword}%");
                    });
                } else {
                    $masterQuery->orWhere($column, 'like', "%{$keyword}%");
                }
            }
        });
    }
}
