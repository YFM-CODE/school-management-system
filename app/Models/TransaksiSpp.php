<?php

namespace App\Models;

use App\Enums\MetodePembayaran;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiSpp extends Model
{
    use Filterable, HasFactory;

    protected $table = 'transaksi_spp';

    protected $fillable = [
        'nomor_kuitansi',
        'tagihan_spp_id',
        'petugas_id',
        'setoran_kasir_id',
        'jumlah_dibayar',
        'waktu_transaksi',
        'metode_pembayaran',
        'nomor_referensi_bank',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_transaksi' => 'datetime',
            'jumlah_dibayar' => 'decimal:2',
            'metode_pembayaran' => MetodePembayaran::class,
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(TagihanSpp::class, 'tagihan_spp_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function setoranKasir(): BelongsTo
    {
        return $this->belongsTo(SetoranKasir::class, 'setoran_kasir_id');
    }
}
