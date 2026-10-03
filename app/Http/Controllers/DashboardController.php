<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use App\Models\DispensasiSpp;
use App\Models\SetoranKasir;
use App\Models\TagihanSpp;
use App\Models\TarifSpp;
use App\Models\TransaksiSpp;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $roleValue = $user->role instanceof PeranPengguna ? $user->role->value : (string) $user->role;

        $ringkasan = [];
        $tagihan = [];

        try {
            $ringkasan = match ($roleValue) {
                PeranPengguna::ADMIN->value => [
                    ['label' => 'Tagihan belum lunas', 'nilai' => TagihanSpp::where('status', '!=', 'lunas')->count()],
                    ['label' => 'Total tunggakan', 'nilai' => TagihanSpp::where('status', '!=', 'lunas')->sum('sisa_tagihan'), 'tipe' => 'rupiah'],
                ],
                PeranPengguna::STAF_TU->value => [
                    ['label' => 'Transaksi hari ini', 'nilai' => TransaksiSpp::where('petugas_id', $user->id)->whereDate('created_at', today())->count()],
                    ['label' => 'Diterima hari ini', 'nilai' => TransaksiSpp::where('petugas_id', $user->id)->whereDate('created_at', today())->sum('jumlah_dibayar'), 'tipe' => 'rupiah'],
                ],
                PeranPengguna::BENDAHARA->value => [
                    ['label' => 'Setoran menunggu verifikasi', 'nilai' => SetoranKasir::where('status', 'menunggu_verifikasi')->count()],
                ],
                PeranPengguna::KEPALA_SEKOLAH->value => [
                    ['label' => 'Dispensasi menunggu keputusan', 'nilai' => DispensasiSpp::where('status_pengajuan', 'diajukan')->count()],
                ],
                PeranPengguna::KOMITE_SEKOLAH->value => [
                    ['label' => 'Tarif menunggu persetujuan', 'nilai' => TarifSpp::where('status_tarif', 'draft')->count()],
                ],
                PeranPengguna::SISWA->value => [
                    ['label' => 'Total sisa kewajiban', 'nilai' => TagihanSpp::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))->sum('sisa_tagihan'), 'tipe' => 'rupiah'],
                ],
                default => [],
            };

            if ($roleValue === PeranPengguna::SISWA->value) {
                $tagihan = TagihanSpp::whereHas('siswa', fn($q) => $q->where('user_id', $user->id))
                    ->where('sisa_tagihan', '>', 0)
                    ->with('siswa:id,nama_lengkap')
                    ->orderBy('jatuh_tempo')
                    ->limit(5)
                    ->get()
                    ->map(fn($t) => [
                        'id' => $t->id,
                        'siswa' => $t->siswa?->nama_lengkap ?? 'Siswa',
                        'bulan' => $t->bulan,
                        'tahun' => $t->tahun,
                        'sisa_tagihan' => $t->sisa_tagihan,
                        'jatuh_tempo' => $t->jatuh_tempo,
                    ])
                    ->all();
            }
        } catch (Throwable $e) {
            report($e);
        }

        return Inertia::render('RoleDashboard', [
            'role'      => $roleValue,
            'ringkasan' => $ringkasan,
            'tagihan'   => $tagihan,
        ]);
    }
}