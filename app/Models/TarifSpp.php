<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarifSpp extends Model
{
    use Filterable, HasFactory;

    protected $table = 'tarif_spp';

    protected $fillable = [
        'tahun_ajaran',
        'tingkat',
        'nominal_standar',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_standar' => 'decimal:2',
        ];
    }

    public function persetujuanKomite(): HasMany
    {
        return $this->hasMany(PersetujuanTarifKomite::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(TagihanSpp::class);
    }
}
