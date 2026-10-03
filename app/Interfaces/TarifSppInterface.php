<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\TarifSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TarifSppInterface
{
    public function semua(): Collection;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;

    public function cariBerdasarkanId(int $id): ?TarifSpp;

    public function simpan(array $data): TarifSpp;

    public function perbarui(int $id, array $data): bool;

    public function hapus(int $id): bool;

    public function ambilBerdasarkanTingkatDanTahun(string $tingkat, string $tahunAjaran): ?TarifSpp;
}
