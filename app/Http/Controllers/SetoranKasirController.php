<?php

namespace App\Http\Controllers;

use App\Filters\GlobalFilter;
use App\Http\Requests\VerifikasiSetoranKasirRequest;
use App\Interfaces\SetoranKasirInterface;
use App\Interfaces\TransaksiSppInterface;
use App\Models\SetoranKasir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SetoranKasirController extends Controller
{
    public function __construct(
        protected SetoranKasirInterface $setoranRepo,
        protected TransaksiSppInterface $transaksiRepo
    ) {}

    public function index(GlobalFilter $filter): Response
    {
        return Inertia::render('SetoranKasir/Index', [
            'setoran' => $this->setoranRepo->paginate($filter),
            'filters' => request()->all(['search']),
        ]);
    }

    public function tutupKasir(Request $request): RedirectResponse
    {
        $petugasId = $request->user()->id;
        $transaksiTunai = $this->transaksiRepo->ambilTransaksiBelumDisetorPetugas($petugasId);

        if ($transaksiTunai->isEmpty()) {
            return redirect()->back()->withErrors([
                'setoran' => 'Tidak ada penerimaan uang tunai yang belum disetor saat ini.',
            ]);
        }

        DB::beginTransaction();
        try {
            $totalUang = $transaksiTunai->sum('jumlah_dibayar');

            $setoran = SetoranKasir::create([
                'nomor_setoran' => 'STR-'.date('YmdHis'),
                'staf_tu_id' => $petugasId,
                'total_tercatat' => $totalUang,
                'tanggal_setoran' => now(),
                'status' => 'menunggu_verifikasi',
            ]);

            $this->transaksiRepo->tautkanKeSetoran($transaksiTunai->pluck('id')->toArray(), $setoran->id);

            DB::commit();

            return redirect()->back()->with('success', 'Kasir berhasil ditutup. Silakan serahkan uang fisik ke Bendahara.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal menutup kasir: '.$th->getMessage()]);
        }
    }

    public function verifikasi(VerifikasiSetoranKasirRequest $request, int $setoranKasir): RedirectResponse
    {
        $setoran = $this->setoranRepo->cariBerdasarkanId($setoranKasir);

        if (! $setoran || $setoran->status === 'diterima') {
            return redirect()->back()->withErrors([
                'setoran' => 'Setoran kasir ini sudah diverifikasi sebelumnya atau tidak ditemukan.',
            ]);
        }

        $validated = $request->validated();
        $selisih = $validated['total_fisik'] - $setoran->total_tercatat;

        DB::beginTransaction();
        try {
            $this->setoranRepo->perbarui($setoranKasir, [
                'bendahara_id' => $request->user()->id,
                'total_fisik' => $validated['total_fisik'],
                'selisih' => $selisih,
                'status' => $validated['status'],
                'catatan_bendahara' => $validated['catatan_bendahara'] ?? null,
                'waktu_verifikasi' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Verifikasi setoran kasir berhasil diperbarui.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal memverifikasi setoran: '.$th->getMessage()]);
        }
    }
}
