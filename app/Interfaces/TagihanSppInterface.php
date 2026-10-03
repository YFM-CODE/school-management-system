<?php

namespace App\Interfaces;

use App\Filters\GlobalFilter;
use App\Models\TagihanSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TagihanSppInterface
{
    public function cariBerdasarkanId(int $id, array $relasi = []): ?TagihanSpp;

    public function daftarTagihanSiswa(int $siswaId): Collection;

    public function daftarBelumLunasSiswa(int $siswaId): Collection;

    public function simpan(array $data): TagihanSpp;

    public function perbarui(int $id, array $data): bool;

    public function cekTagihanAda(int $siswaId, int $bulan, int $tahun): bool;

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator;
}
