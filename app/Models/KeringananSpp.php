<?php

namespace App\Models;

use App\Enums\TipeKeringanan;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeringananSpp extends Model
{
    use Filterable, HasFactory;

    protected $table = 'keringanan_spp';

    protected $fillable = [
        'nama_program',
        'tipe_potongan',
        'nilai_potongan',
        'syarat_ketentuan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'nilai_potongan' => 'decimal:2',
            'tipe_potongan' => TipeKeringanan::class,
            'aktif' => 'boolean',
        ];
    }

    public function penerima(): HasMany
    {
        return $this->hasMany(SiswaKeringananSpp::class);
    }
}
