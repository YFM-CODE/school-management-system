<?php

namespace App\Enums;

enum MetodePembayaran: string
{
    case TUNAI = 'tunai';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::TUNAI => 'Tunai (Kasir TU)',
            self::TRANSFER => 'Transfer Bank Sekolah',
        };
    }
}
