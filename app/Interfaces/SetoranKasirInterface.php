<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\SetoranKasir;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SetoranKasirInterface
{
    public function cariBerdasarkanId(int $id): ?SetoranKasir;

    public function simpan(array $data): SetoranKasir;

    public function perbarui(int $id, array $data): bool;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;
}
