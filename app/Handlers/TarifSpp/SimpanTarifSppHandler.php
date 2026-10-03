<?php

namespace App\Handlers\TarifSpp;

use App\Interfaces\TarifSppInterface;
use App\Models\TarifSpp;
use Illuminate\Validation\ValidationException;

class SimpanTarifSppHandler
{
    public function __construct(
        protected TarifSppInterface $tarifRepo
    ) {}

    public function handle(array $data): TarifSpp
    {
        // Validasi aturan bisnis: Tidak boleh ada tarif ganda untuk tingkat & tahun ajaran yang sama
        $sudahAda = $this->tarifRepo->ambilBerdasarkanTingkatDanTahun(
            $data['tingkat'],
            $data['tahun_ajaran']
        );

        if ($sudahAda) {
            throw ValidationException::withMessages([
                'tingkat' => 'Tarif SPP untuk tingkat '.$data['tingkat'].' pada tahun ajaran '.$data['tahun_ajaran'].' sudah terdaftar.',
            ]);
        }

        return $this->tarifRepo->simpan([
            'tahun_ajaran' => $data['tahun_ajaran'],
            'tingkat' => $data['tingkat'],
            'nominal_standar' => $data['nominal_standar'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }
}
