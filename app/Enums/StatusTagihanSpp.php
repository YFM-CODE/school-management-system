<?php

namespace App\Enums;

enum StatusTagihanSpp: string
{
    case BELUM_BAYAR = 'belum_bayar';
    case SEBAGIAN = 'sebagian';
    case LUNAS = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::BELUM_BAYAR => 'Belum Bayar',
            self::SEBAGIAN => 'Dicicil Sebagian',
            self::LUNAS => 'Lunas',
        };
    }
}
