<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\KeringananSppInterface;
use App\Models\KeringananSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class KeringananSppRepository implements KeringananSppInterface
{
    public function semuaAktif(): Collection
    {
        return KeringananSpp::where('aktif', true)->latest()->get();
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return KeringananSpp::query()
            ->filterGlobal($filter, ['nama_program', 'syarat_ketentuan'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function cariBerdasarkanId(int $id): ?KeringananSpp
    {
        return KeringananSpp::find($id);
    }

    public function simpan(array $data): KeringananSpp
    {
        return KeringananSpp::create($data);
    }

    public function perbarui(int $id, array $data): bool
    {
        $keringanan = KeringananSpp::find($id);

        return $keringanan ? $keringanan->update($data) : false;
    }

    public function hapus(int $id): bool
    {
        $keringanan = KeringananSpp::find($id);

        return $keringanan ? (bool) $keringanan->delete() : false;
    }
}
