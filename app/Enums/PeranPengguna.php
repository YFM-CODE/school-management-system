<?php

namespace App\Enums;

enum PeranPengguna: string
{
    case ADMIN = 'admin';
    case BENDAHARA = 'bendahara';
    case STAF_TU = 'staf_tu';
    case KEPALA_SEKOLAH = 'kepala_sekolah';
    case KOMITE_SEKOLAH = 'komite_sekolah';
    case SISWA = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator Sistem',
            self::BENDAHARA => 'Bendahara Sekolah',
            self::STAF_TU => 'Staf Tata Usaha',
            self::KEPALA_SEKOLAH => 'Kepala Sekolah',
            self::KOMITE_SEKOLAH => 'Komite Sekolah',
            self::SISWA => 'Siswa / Wali Murid',
        };
    }
}
