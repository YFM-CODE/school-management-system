<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\DispensasiSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DispensasiSppInterface
{
    public function cariBerdasarkanId(int $id): ?DispensasiSpp;

    public function simpan(array $data): DispensasiSpp;

    public function perbarui(int $id, array $data): bool;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;
}
