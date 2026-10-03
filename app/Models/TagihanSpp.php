<?php

namespace App\Models;

use App\Enums\StatusTagihanSpp;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanSpp extends Model
{
    use Filterable, HasFactory;

    protected $table = 'tagihan_spp';

    protected $fillable = [
        'siswa_id',
        'tarif_spp_id',
        'keringanan_spp_id',
        'bulan',
        'tahun',
        'nominal_asli',
        'nominal_potongan',
        'nominal_tagihan',
        'total_terbayar',
        'sisa_tagihan',
        'status',
        'jatuh_tempo',
    ];

    protected function casts(): array
    {
        return [
            'nominal_asli' => 'decimal:2',
            'nominal_potongan' => 'decimal:2',
            'nominal_tagihan' => 'decimal:2',
            'total_terbayar' => 'decimal:2',
            'sisa_tagihan' => 'decimal:2',
            'status' => StatusTagihanSpp::class,
            'jatuh_tempo' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(TarifSpp::class, 'tarif_spp_id');
    }

    public function keringanan(): BelongsTo
    {
        return $this->belongsTo(KeringananSpp::class, 'keringanan_spp_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiSpp::class);
    }

    public function dispensasi(): HasMany
    {
        return $this->hasMany(DispensasiSpp::class);
    }
}
