<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PETUGAS = 'petugas';
    case SISWA = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PETUGAS => 'Petugas',
            self::SISWA => 'Siswa',
        };
    }
}
