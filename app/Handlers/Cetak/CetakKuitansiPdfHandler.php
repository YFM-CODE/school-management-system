<?php

namespace App\Handlers\Cetak;

use App\Interfaces\TransaksiSppInterface;
use Illuminate\Validation\ValidationException;

class CetakKuitansiPdfHandler
{
    public function __construct(
        protected TransaksiSppInterface $transaksiRepo
    ) {}

    public function handle(int $transaksiSppId): array
    {
        $transaksi = $this->transaksiRepo->cariBerdasarkanId($transaksiSppId);

        if (! $transaksi) {
            throw ValidationException::withMessages([
                'transaksi' => 'Data transaksi SPP tidak ditemukan.',
            ]);
        }

        $tagihan = $transaksi->tagihan;
        $siswa = $tagihan->siswa;

        return [
            'transaksi' => $transaksi,
            'nomor_transaksi' => $transaksi->nomor_transaksi,
            'nama_siswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
            'kelas' => $siswa->kelas?->nama_kelas ?? '-',
            'periode' => 'Bulan '.$tagihan->bulan.' / Tahun '.$tagihan->tahun,
            'jumlah_dibayar' => $transaksi->jumlah_dibayar,
            'metode' => $transaksi->metode_pembayaran,
            'petugas' => $transaksi->petugas?->name ?? 'Kasir Loket',
            'tanggal_bayar' => $transaksi->created_at->translatedFormat('d F Y H:i'),
            'sisa_tagihan' => max(0, $tagihan->sisa_tagihan),
        ];
    }
}
