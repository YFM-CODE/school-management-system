<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\TransaksiSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransaksiSppInterface
{
    public function cariBerdasarkanId(int $id): ?TransaksiSpp;

    public function simpan(array $data): TransaksiSpp;

    public function ambilTransaksiBelumDisetorPetugas(int $petugasId): Collection;

    public function tautkanKeSetoran(array $transaksiIds, int $setoranKasirId): int;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;
}
