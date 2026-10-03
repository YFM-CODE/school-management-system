<?php

namespace App\Models;

use App\Enums\StatusPersetujuanTarif;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanTarifKomite extends Model
{
    use HasFactory;

    protected $table = 'persetujuan_tarif_komite';

    protected $fillable = [
        'tarif_spp_id',
        'ketua_komite_id',
        'nomor_berita_acara',
        'tanggal_kesepakatan',
        'dokumen_ba_pdf',
        'status_persetujuan',
        'catatan_komite',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kesepakatan' => 'date',
            'status_persetujuan' => StatusPersetujuanTarif::class,
        ];
    }

    public function tarifSpp(): BelongsTo
    {
        return $this->belongsTo(TarifSpp::class);
    }

    public function ketuaKomite(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_komite_id');
    }
}
