<?php

namespace App\Enums;

enum Permission: string
{
    // Akses Dashboard & Manajemen Sekolah
    case VIEW_EXECUTIVE_DASHBOARD = 'view_executive_dashboard';
    case MANAGE_KURIKULUM = 'manage_kurikulum';
    case MANAGE_KESISWAAN = 'manage_kesiswaan';
    case MANAGE_SARPRAS = 'manage_sarpras';
    case MANAGE_HUMAS = 'manage_humas';
    case MANAGE_HUBIN = 'manage_hubin';

    // Akademik
    case MANAGE_JURUSAN = 'manage_jurusan';
    case MANAGE_WALI_KELAS = 'manage_wali_kelas';
    case INPUT_NILAI = 'input_nilai';
    case MANAGE_BK = 'manage_bk';

    // Administrasi & Layanan
    case MANAGE_TU = 'manage_tu';
    case MANAGE_PERPUSTAKAAN = 'manage_perpustakaan';
    case MANAGE_LABORATORIUM = 'manage_laboratorium';
    case MANAGE_TEKNIS_KOMPUTER = 'manage_teknis_komputer';

    // Pengguna Akhir (Siswa & Wali Murid)
    case VIEW_ACADEMIC_REPORT = 'view_academic_report';
    case VIEW_BILLING = 'view_billing';

    public function label(): string
    {
        return match ($this) {
            self::VIEW_EXECUTIVE_DASHBOARD => 'Melihat Dashboard Eksekutif',
            self::MANAGE_KURIKULUM => 'Kelola Data Kurikulum',
            self::MANAGE_KESISWAAN => 'Kelola Data Kesiswaan',
            self::MANAGE_SARPRAS => 'Kelola Sarana dan Prasarana',
            self::MANAGE_HUMAS => 'Kelola Hubungan Masyarakat',
            self::MANAGE_HUBIN => 'Kelola Hubungan Industri',
            self::MANAGE_JURUSAN => 'Kelola Program Keahlian / Jurusan',
            self::MANAGE_WALI_KELAS => 'Kelola Kelas Perwalian',
            self::INPUT_NILAI => 'Input dan Ubah Nilai Siswa',
            self::MANAGE_BK => 'Kelola Bimbingan Konseling',
            self::MANAGE_TU => 'Kelola Administrasi & Tata Usaha',
            self::MANAGE_PERPUSTAKAAN => 'Kelola Perpustakaan',
            self::MANAGE_LABORATORIUM => 'Kelola Laboratorium',
            self::MANAGE_TEKNIS_KOMPUTER => 'Kelola Sarana & Teknis Komputer',
            self::VIEW_ACADEMIC_REPORT => 'Melihat Rapor dan Jadwal Belajar',
            self::VIEW_BILLING => 'Melihat Rincian Tagihan & Biaya',
        };
    }
}
