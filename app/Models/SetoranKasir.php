<?php

namespace App\Models;

use App\Enums\StatusSetoranKasir;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SetoranKasir extends Model
{
    use Filterable, HasFactory;

    protected $table = 'setoran_kasir';

    protected $fillable = [
        'nomor_setoran',
        'staf_tu_id',
        'bendahara_id',
        'tanggal_setoran',
        'total_sistem',
        'total_fisik',
        'selisih',
        'status',
        'catatan_bendahara',
        'waktu_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_setoran' => 'date',
            'waktu_verifikasi' => 'datetime',
            'total_sistem' => 'decimal:2',
            'total_fisik' => 'decimal:2',
            'selisih' => 'decimal:2',
            'status' => StatusSetoranKasir::class,
        ];
    }

    public function stafTu(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staf_tu_id');
    }

    public function bendahara(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bendahara_id');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiSpp::class);
    }
}
