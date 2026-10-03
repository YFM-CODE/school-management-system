<?php

namespace App\Handlers\TarifSpp;

use App\Interfaces\TarifSppInterface;
use Illuminate\Validation\ValidationException;

class PerbaruiTarifSppHandler
{
    public function __construct(
        protected TarifSppInterface $tarifRepo
    ) {}

    public function handle(int $id, array $data): bool
    {
        $tarif = $this->tarifRepo->cariBerdasarkanId($id);

        if (! $tarif) {
            throw ValidationException::withMessages([
                'id' => 'Data tarif SPP tidak ditemukan.',
            ]);
        }

        // Cek duplikasi jika tingkat atau tahun ajaran diubah
        $duplikat = $this->tarifRepo->ambilBerdasarkanTingkatDanTahun(
            $data['tingkat'],
            $data['tahun_ajaran']
        );

        if ($duplikat && $duplikat->id !== $id) {
            throw ValidationException::withMessages([
                'tingkat' => 'Tarif SPP dengan kombinasi tingkat dan tahun ajaran tersebut sudah ada.',
            ]);
        }

        return $this->tarifRepo->perbarui($id, [
            'tahun_ajaran' => $data['tahun_ajaran'],
            'tingkat' => $data['tingkat'],
            'nominal_standar' => $data['nominal_standar'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }
}
