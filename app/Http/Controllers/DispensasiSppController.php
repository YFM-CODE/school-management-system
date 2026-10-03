<?php

namespace App\Http\Controllers;

use App\Filters\GlobalFilter;
use App\Http\Requests\SimpanPengajuanDispensasiRequest;
use App\Interfaces\DispensasiSppInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DispensasiSppController extends Controller
{
    public function __construct(
        protected DispensasiSppInterface $dispensasiRepo
    ) {}

    public function index(GlobalFilter $filter): Response
    {
        return Inertia::render('DispensasiSpp/Index', [
            'dispensasi' => $this->dispensasiRepo->paginate($filter),
            'filters' => request()->all(['search']),
        ]);
    }

    public function store(SimpanPengajuanDispensasiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $pathDokumen = null;
            if ($request->hasFile('dokumen_pendukung')) {
                $pathDokumen = $request->file('dokumen_pendukung')->store('dokumen_dispensasi', 'public');
            }

            $this->dispensasiRepo->simpan([
                'tagihan_spp_id' => $validated['tagihan_spp_id'],
                'pemohon_id' => $request->user()->id,
                'batas_waktu_baru' => $validated['batas_waktu_baru'],
                'alasan_penundaan' => $validated['alasan_penundaan'],
                'dokumen_pendukung' => $pathDokumen,
                'status' => 'menunggu_persetujuan',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pengajuan permohonan dispensasi berhasil diajukan.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal mengajukan dispensasi: '.$th->getMessage()]);
        }
    }

    public function persetujuan(Request $request, int $dispensasiSpp): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:disetujui,ditolak'],
            'catatan_pimpinan' => ['nullable', 'string', 'max:500'],
        ]);

        $dispensasi = $this->dispensasiRepo->cariBerdasarkanId($dispensasiSpp);

        if (! $dispensasi) {
            return redirect()->back()->withErrors(['dispensasi' => 'Data permohonan dispensasi tidak ditemukan.']);
        }

        DB::beginTransaction();
        try {
            $this->dispensasiRepo->perbarui($dispensasiSpp, [
                'kepala_sekolah_id' => $request->user()->id,
                'status' => $validated['status'],
                'catatan_pimpinan' => $validated['catatan_pimpinan'] ?? null,
                'tanggal_diproses' => now(),
            ]);

            if ($validated['status'] === 'disetujui') {
                $dispensasi->tagihan->update([
                    'jatuh_tempo' => $dispensasi->batas_waktu_baru,
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Keputusan dispensasi berhasil diproses.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal memproses persetujuan dispensasi: '.$th->getMessage()]);
        }
    }
}
