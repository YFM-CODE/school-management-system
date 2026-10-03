<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\TarifSppInterface;
use App\Models\TarifSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TarifSppRepository implements TarifSppInterface
{
    public function semua(): Collection
    {
        return TarifSpp::with(['persetujuanKomite.ketuaKomite'])->latest()->get();
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return TarifSpp::with(['persetujuanKomite.ketuaKomite'])
            ->filterGlobal($filter, ['tahun_ajaran', 'tingkat', 'keterangan'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function cariBerdasarkanId(int $id): ?TarifSpp
    {
        return TarifSpp::with(['persetujuanKomite.ketuaKomite'])->find($id);
    }

    public function simpan(array $data): TarifSpp
    {
        return TarifSpp::create($data);
    }

    public function perbarui(int $id, array $data): bool
    {
        $tarif = TarifSpp::find($id);

        return $tarif ? $tarif->update($data) : false;
    }

    public function hapus(int $id): bool
    {
        $tarif = TarifSpp::find($id);

        return $tarif ? (bool) $tarif->delete() : false;
    }

    public function ambilBerdasarkanTingkatDanTahun(string $tingkat, string $tahunAjaran): ?TarifSpp
    {
        return TarifSpp::where('tingkat', $tingkat)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();
    }
}
