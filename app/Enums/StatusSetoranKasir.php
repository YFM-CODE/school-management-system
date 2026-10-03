<?php

namespace App\Enums;

enum StatusSetoranKasir: string
{
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case DITERIMA = 'diterima';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi Bendahara',
            self::DITERIMA => 'Diterima & Terkunci',
            self::DITOLAK => 'Ditolak (Ada Selisih Uang Fisik)',
        };
    }
}
