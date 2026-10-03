<?php

namespace App\Repositories;

use App\Filters\GlobalFilter;
use App\Interfaces\TagihanSppInterface;
use App\Models\TagihanSpp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TagihanSppRepository implements TagihanSppInterface
{
    public function cariBerdasarkanId(int $id, array $relasi = []): ?TagihanSpp
    {
        return TagihanSpp::with($relasi)->find($id);
    }

    public function daftarTagihanSiswa(int $siswaId): Collection
    {
        return TagihanSpp::with(['transaksi', 'dispensasi'])
            ->where('siswa_id', $siswaId)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }

    public function daftarBelumLunasSiswa(int $siswaId): Collection
    {
        return TagihanSpp::where('siswa_id', $siswaId)
            ->where('status', '!=', 'lunas')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();
    }

    public function simpan(array $data): TagihanSpp
    {
        return TagihanSpp::create($data);
    }

    public function perbarui(int $id, array $data): bool
    {
        $tagihan = TagihanSpp::find($id);

        return $tagihan ? $tagihan->update($data) : false;
    }

    public function cekTagihanAda(int $siswaId, int $bulan, int $tahun): bool
    {
        return TagihanSpp::where('siswa_id', $siswaId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();
    }

    public function paginate(GlobalFilter $filter, int $perPage = 15): LengthAwarePaginator
    {
        return TagihanSpp::with(['siswa.kelas', 'tarif'])
            ->filterGlobal($filter, ['siswa.nama_lengkap', 'siswa.nis', 'nomor_tagihan'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
