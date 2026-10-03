<?php

namespace App\Http\Controllers;

use App\Filters\GlobalFilter;
use App\Http\Requests\SimpanPembayaranSppRequest;
use App\Interfaces\TagihanSppInterface;
use App\Interfaces\TransaksiSppInterface;
use App\Models\Siswa;
use App\Models\TransaksiSpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PembayaranSppController extends Controller
{
    public function __construct(
        protected TransaksiSppInterface $transaksiRepo,
        protected TagihanSppInterface $tagihanRepo
    ) {}

    public function index(GlobalFilter $filter): Response
    {
        return Inertia::render('PembayaranSpp/Index', [
            'transaksi' => $this->transaksiRepo->paginate($filter),
            'filters' => request()->all(['search']),
        ]);
    }

    public function cariSiswa(Request $request): JsonResponse
    {
        $keyword = $request->query('q');

        $siswa = Siswa::with(['kelas', 'keringanan'])
            ->where('nis', 'like', "%{$keyword}%")
            ->orWhere('nama_lengkap', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json($siswa);
    }

    public function store(SimpanPembayaranSppRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $tagihan = $this->tagihanRepo->cariBerdasarkanId($data['tagihan_spp_id']);

            if (! $tagihan || $tagihan->status === 'lunas') {
                throw ValidationException::withMessages([
                    'tagihan_spp_id' => 'Tagihan ini sudah lunas atau tidak ditemukan.',
                ]);
            }

            if ($data['jumlah_dibayar'] > $tagihan->sisa_tagihan) {
                throw ValidationException::withMessages([
                    'jumlah_dibayar' => 'Jumlah bayar tidak boleh melebihi sisa tagihan (Rp '.number_format($tagihan->sisa_tagihan, 0, ',', '.').').',
                ]);
            }

            // 1. Simpan Transaksi Kasir
            $trx = TransaksiSpp::create([
                'nomor_transaksi' => 'TRX-'.date('YmdHis').'-'.rand(100, 999),
                'tagihan_spp_id' => $tagihan->id,
                'petugas_id' => $request->user()->id,
                'jumlah_dibayar' => $data['jumlah_dibayar'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'nomor_referensi_bank' => $data['nomor_referensi_bank'] ?? null,
                'catatan' => $data['catatan'] ?? null,
            ]);

            // 2. Potong Sisa Tagihan
            $sisaBaru = $tagihan->sisa_tagihan - $data['jumlah_dibayar'];
            $tagihan->update([
                'sisa_tagihan' => $sisaBaru,
                'status' => $sisaBaru <= 0 ? 'lunas' : 'sebagian',
            ]);

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Pembayaran berhasil disimpan.',
                'transaksi_id' => $trx->id,
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal memproses pembayaran: '.$th->getMessage()]);
        }
    }
}
