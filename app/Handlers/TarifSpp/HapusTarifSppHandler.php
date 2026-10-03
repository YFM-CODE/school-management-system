<?php

namespace App\Handlers\TarifSpp;

use App\Interfaces\TarifSppInterface;
use Illuminate\Validation\ValidationException;

class HapusTarifSppHandler
{
    public function __construct(
        protected TarifSppInterface $tarifRepo
    ) {}

    public function handle(int $id): bool
    {
        $tarif = $this->tarifRepo->cariBerdasarkanId($id);

        if (! $tarif) {
            throw ValidationException::withMessages([
                'id' => 'Data tarif SPP tidak ditemukan.',
            ]);
        }

        // Tagihan yang sudah terbit tidak boleh kehilangan induk tarifnya
        if ($tarif->tagihan()->exists()) {
            throw ValidationException::withMessages([
                'tarif' => 'Tarif SPP ini tidak dapat dihapus karena sudah memiliki tagihan yang digenerate untuk siswa.',
            ]);
        }

        return $this->tarifRepo->hapus($id);
    }
}
