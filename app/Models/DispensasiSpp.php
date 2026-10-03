<?php

namespace App\Models;

use App\Enums\StatusDispensasi;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensasiSpp extends Model
{
    use Filterable, HasFactory;

    protected $table = 'dispensasi_spp';

    protected $fillable = [
        'tagihan_spp_id',
        'disetujui_oleh',
        'tanggal_perjanjian',
        'batas_waktu_baru',
        'alasan_penundaan',
        'dokumen_pendukung',
        'status_pengajuan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_perjanjian' => 'date',
            'batas_waktu_baru' => 'date',
            'status_pengajuan' => StatusDispensasi::class,
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(TagihanSpp::class, 'tagihan_spp_id');
    }

    public function kepalaSekolah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
