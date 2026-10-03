<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\DispensasiSppInterface;
use App\Models\DispensasiSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DispensasiSppRepository implements DispensasiSppInterface
{
    public function cariBerdasarkanId(int $id): ?DispensasiSpp
    {
        return DispensasiSpp::with(['tagihan.siswa.kelas', 'pemohon', 'kepalaSekolah'])->find($id);
    }

    public function simpan(array $data): DispensasiSpp
    {
        return DispensasiSpp::create($data);
    }

    public function perbarui(int $id, array $data): bool
    {
        $dispensasi = DispensasiSpp::find($id);

        return $dispensasi ? $dispensasi->update($data) : false;
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return DispensasiSpp::with(['tagihan.siswa.kelas', 'pemohon', 'kepalaSekolah'])
            ->filterGlobal($filter, ['tagihan.siswa.nama_lengkap', 'tagihan.siswa.nis', 'alasan_penundaan'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
