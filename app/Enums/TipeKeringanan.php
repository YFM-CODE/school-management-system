<?php

namespace App\Enums;

enum TipeKeringanan: string
{
    case PERSENTASE = 'persentase';
    case NOMINAL = 'nominal';

    public function label(): string
    {
        return match ($this) {
            self::PERSENTASE => 'Persentase (%)',
            self::NOMINAL => 'Nominal Tetap (Rp)',
        };
    }
}
