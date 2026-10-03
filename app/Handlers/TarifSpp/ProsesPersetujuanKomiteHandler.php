<?php

namespace App\Handlers\TarifSpp;

use App\Interfaces\TarifSppInterface;
use App\Models\PersetujuanTarifKomite;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProsesPersetujuanKomiteHandler
{
    public function __construct(
        protected TarifSppInterface $tarifRepo
    ) {}

    public function handle(int $tarifSppId, int $ketuaKomiteUserId, array $data, ?UploadedFile $filePdf = null): PersetujuanTarifKomite
    {
        $tarif = $this->tarifRepo->cariBerdasarkanId($tarifSppId);

        if (! $tarif) {
            throw ValidationException::withMessages([
                'tarif_spp_id' => 'Data tarif SPP tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($tarif, $ketuaKomiteUserId, $data, $filePdf) {
            $pathDokumen = null;

            // Simpan berkas Berita Acara jika diunggah
            if ($filePdf) {
                $pathDokumen = $filePdf->store('berita_acara_tarif', 'public');
            }

            // Simpan / update keputusan komite
            return PersetujuanTarifKomite::updateOrCreate(
                ['tarif_spp_id' => $tarif->id],
                [
                    'ketua_komite_id' => $ketuaKomiteUserId,
                    'nomor_berita_acara' => $data['nomor_berita_acara'],
                    'tanggal_kesepakatan' => $data['tanggal_kesepakatan'],
                    'status_persetujuan' => $data['status_persetujuan'],
                    'file_berita_acara' => $pathDokumen ?? $tarif->persetujuanKomite?->file_berita_acara,
                    'catatan_komite' => $data['catatan_komite'] ?? null,
                ]
            );
        });
    }
}
