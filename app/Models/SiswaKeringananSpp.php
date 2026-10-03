<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaKeringananSpp extends Model
{
    use HasFactory;

    protected $table = 'siswa_keringanan_spp';

    protected $fillable = [
        'siswa_id',
        'keringanan_spp_id',
        'tahun_ajaran',
        'nomor_sk',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function keringanan(): BelongsTo
    {
        return $this->belongsTo(KeringananSpp::class, 'keringanan_spp_id');
    }
}
