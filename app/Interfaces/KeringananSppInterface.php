<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\KeringananSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface KeringananSppInterface
{
    public function semuaAktif(): Collection;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;

    public function cariBerdasarkanId(int $id): ?KeringananSpp;

    public function simpan(array $data): KeringananSpp;

    public function perbarui(int $id, array $data): bool;

    public function hapus(int $id): bool;
}
