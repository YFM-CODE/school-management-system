<?php

namespace App\Enums;

enum StatusPersetujuanTarif: string
{
    case DRAFT = 'draft';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Menunggu Persetujuan Komite',
            self::DISETUJUI => 'Disetujui Komite & Aktif',
            self::DITOLAK => 'Ditolak Komite',
        };
    }
}
