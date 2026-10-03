<?php

namespace App\Http\Controllers;

use App\Filters\GlobalFilter;
use App\Interfaces\KeringananSppInterface;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class KeringananSppController extends Controller
{
    public function __construct(
        protected KeringananSppInterface $keringananRepo
    ) {}

    public function index(GlobalFilter $filter): Response
    {
        return Inertia::render('KeringananSpp/Index', [
            'keringanan' => $this->keringananRepo->paginate($filter),
            'filters' => request()->all(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_program' => ['required', 'string', 'max:100'],
            'tipe_potongan' => ['required', 'in:persen,nominal'],
            'nilai_potongan' => ['required', 'numeric', 'min:1'],
            'syarat_ketentuan' => ['nullable', 'string', 'max:1000'],
            'aktif' => ['boolean'],
        ]);

        DB::beginTransaction();
        try {
            $this->keringananRepo->simpan($validated);
            DB::commit();

            return redirect()->back()->with('success', 'Program keringanan SPP berhasil dibuat.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal membuat program keringanan: '.$th->getMessage()]);
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_program' => ['required', 'string', 'max:100'],
            'tipe_potongan' => ['required', 'in:persen,nominal'],
            'nilai_potongan' => ['required', 'numeric', 'min:1'],
            'syarat_ketentuan' => ['nullable', 'string', 'max:1000'],
            'aktif' => ['boolean'],
        ]);

        DB::beginTransaction();
        try {
            $this->keringananRepo->perbarui($id, $validated);
            DB::commit();

            return redirect()->back()->with('success', 'Program keringanan SPP berhasil diperbarui.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui program keringanan: '.$th->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $this->keringananRepo->hapus($id);
            DB::commit();

            return redirect()->back()->with('success', 'Program keringanan berhasil dihapus.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal menghapus program keringanan: '.$th->getMessage()]);
        }
    }

    public function tetapkanSiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'keringanan_spp_id' => ['nullable', 'exists:keringanan_spp,id'],
        ]);

        DB::beginTransaction();
        try {
            $siswa = Siswa::findOrFail($validated['siswa_id']);
            $siswa->update(['keringanan_spp_id' => $validated['keringanan_spp_id']]);
            DB::commit();

            return redirect()->back()->with('success', 'Status keringanan siswa berhasil diperbarui.');
        } catch (Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal menetapkan keringanan ke siswa: '.$th->getMessage()]);
        }
    }
}
