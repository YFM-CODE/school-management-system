<?php

namespace App\Http\Controllers;

use App\Enums\PeranPengguna;
use App\Filters\GlobalFilter;
use App\Interfaces\TagihanSppInterface;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\TarifSpp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TagihanSppController extends Controller
{
    public function __construct(
        protected TagihanSppInterface $tagihanRepo
    ) {}

    public function index(Request $request, GlobalFilter $filter): Response
    {
        $user = $request->user();
        $roleValue = is_object($user->role) ? $user->role->value : $user->role;

        if ($roleValue === PeranPengguna::SISWA->value) {
            $siswa = Siswa::where('user_id', $user->id)->firstOrFail();
            $tagihan = $this->tagihanRepo->daftarTagihanSiswa($siswa->id);

            return Inertia::render('Siswa/TagihanSaya', [
                'tagihan' => $tagihan,
            ]);
        }

        return Inertia::render('TagihanSpp/Index', [
            'tagihan' => $this->tagihanRepo->paginate($filter),
            'filters' => request()->all(['search', 'bulan', 'tahun', 'status', 'siswa__kelas_id']),
        ]);
    }

    public function generateBulanan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['required', 'string'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020'],
            'jatuh_tempo' => ['required', 'date'],
        ]);

        DB::beginTransaction();
        try {
            $daftarSiswa = Siswa::with(['kelas', 'keringanan'])->where('status_aktif', true)->get();

            foreach ($daftarSiswa as $siswa) {
                if ($this->tagihanRepo->cekTagihanAda($siswa->id, $validated['bulan'], $validated['tahun'])) {
                    continue;
                }

                $tarif = TarifSpp::where('tingkat', $siswa->kelas?->tingkat)
                    ->where('tahun_ajaran', $validated['tahun_ajaran'])
                    ->first();

                if (! $tarif) {
                    continue;
                }

                $nominalAwal = $tarif->nominal_standar;
                $potongan = 0;

                if ($siswa->keringanan && $siswa->keringanan->aktif) {
                    $potongan = $siswa->keringanan->tipe_potongan === 'persen'
                        ? ($nominalAwal * ($siswa->keringanan->nilai_potongan / 100))
                        : $siswa->keringanan->nilai_potongan;
                }

                $totalHarusDibayar = max(0, $nominalAwal - $potongan);

                TagihanSpp::create([
                    'nomor_tagihan' => 'TAG-'.$validated['tahun'].str_pad($validated['bulan'], 2, '0', STR_PAD_LEFT).'-'.$siswa->nis,
                    'siswa_id' => $siswa->id,
                    'tarif_spp_id' => $tarif->id,
                    'bulan' => $validated['bulan'],
                    'tahun' => $validated['tahun'],
                    'nominal_awal' => $nominalAwal,
                    'potongan' => $potongan,
                    'total_harus_bayar' => $totalHarusDibayar,
                    'sisa_tagihan' => $totalHarusDibayar,
                    'jatuh_tempo' => $validated['jatuh_tempo'],
                    'status' => 'belum_lunas',
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Tagihan bulanan berhasil digenerate.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal mengenerate tagihan: '.$th->getMessage()]);
        }
    }
}
