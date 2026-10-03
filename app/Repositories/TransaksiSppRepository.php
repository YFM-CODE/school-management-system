<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\TransaksiSppInterface;
use App\Models\TransaksiSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransaksiSppRepository implements TransaksiSppInterface
{
    public function cariBerdasarkanId(int $id): ?TransaksiSpp
    {
        return TransaksiSpp::with(['tagihan.siswa.kelas', 'petugas'])->find($id);
    }

    public function simpan(array $data): TransaksiSpp
    {
        return TransaksiSpp::create($data);
    }

    public function ambilTransaksiBelumDisetorPetugas(int $petugasId): Collection
    {
        return TransaksiSpp::with(['tagihan.siswa'])
            ->where('petugas_id', $petugasId)
            ->whereNull('setoran_kasir_id')
            ->where('metode_pembayaran', 'tunai')
            ->latest()
            ->get();
    }

    public function tautkanKeSetoran(array $transaksiIds, int $setoranKasirId): int
    {
        return TransaksiSpp::whereIn('id', $transaksiIds)
            ->update(['setoran_kasir_id' => $setoranKasirId]);
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return TransaksiSpp::with(['tagihan.siswa.kelas', 'petugas'])
            ->filterGlobal($filter, ['nomor_transaksi', 'tagihan.siswa.nama_lengkap', 'tagihan.siswa.nis'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
