<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\SetoranKasirInterface;
use App\Models\SetoranKasir;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SetoranKasirRepository implements SetoranKasirInterface
{
    public function cariBerdasarkanId(int $id): ?SetoranKasir
    {
        return SetoranKasir::with(['stafTu', 'bendahara', 'transaksi.tagihan.siswa'])->find($id);
    }

    public function simpan(array $data): SetoranKasir
    {
        return SetoranKasir::create($data);
    }

    public function perbarui(int $id, array $data): bool
    {
        $setoran = SetoranKasir::find($id);

        return $setoran ? $setoran->update($data) : false;
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return SetoranKasir::with(['stafTu', 'bendahara'])
            ->filterGlobal($filter, ['nomor_setoran', 'stafTu.name', 'bendahara.name'])
            ->latest('tanggal_setoran')
            ->paginate($perPage)
            ->withQueryString();
    }
}
