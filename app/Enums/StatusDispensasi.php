<?php

namespace App\Enums;

enum StatusDispensasi: string
{
    case MENUNGGU = 'menunggu';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU => 'Menunggu Persetujuan Kepsek',
            self::DISETUJUI => 'Dispensasi Disetujui',
            self::DITOLAK => 'Dispensasi Ditolak',
            self::SELESAI => 'Tenggat Waktu Berakhir',
        };
    }
}
