<?php

use App\Enums\PeranPengguna;
use App\Http\Controllers\CetakKuitansiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispensasiSppController;
use App\Http\Controllers\KeringananSppController;
use App\Http\Controllers\PembayaranSppController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetoranKasirController;
use App\Http\Controllers\TagihanSppController;
use App\Http\Controllers\TarifSppController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function (Request $request) {
    $role = $request->user()->role;
    $roleValue = $role instanceof PeranPengguna ? $role->value : (string) $role;

    return match ($roleValue) {
        PeranPengguna::ADMIN->value => redirect()->route('admin.dashboard'),
        PeranPengguna::BENDAHARA->value => redirect()->route('bendahara.dashboard'),
        PeranPengguna::STAF_TU->value => redirect()->route('tu.dashboard'),
        PeranPengguna::KEPALA_SEKOLAH->value => redirect()->route('kepsek.dashboard'),
        PeranPengguna::KOMITE_SEKOLAH->value => redirect()->route('komite.dashboard'),
        PeranPengguna::SISWA->value => redirect()->route('siswa.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('tarif-spp', TarifSppController::class);
    Route::resource('keringanan-spp', KeringananSppController::class);
    Route::post('/keringanan-spp/tetapkan-siswa', [KeringananSppController::class, 'tetapkanSiswa'])->name('keringanan-spp.tetapkan-siswa');
    Route::get('/tagihan-spp', [TagihanSppController::class, 'index'])->name('tagihan-spp.index');
    Route::post('/tagihan-spp/generate-bulanan', [TagihanSppController::class, 'generateBulanan'])->name('tagihan-spp.generate-bulanan');
});

Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/setoran-kasir', [SetoranKasirController::class, 'index'])->name('setoran-kasir.index');
    Route::post('/setoran-kasir/{setoranKasir}/verifikasi', [SetoranKasirController::class, 'verifikasi'])->name('setoran-kasir.verifikasi');
    Route::get('/tagihan-spp', [TagihanSppController::class, 'index'])->name('tagihan-spp.index');
    Route::post('/tagihan-spp/generate-bulanan', [TagihanSppController::class, 'generateBulanan'])->name('tagihan-spp.generate-bulanan');
});

Route::middleware(['auth', 'role:staf_tu'])->prefix('tu')->name('tu.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/pembayaran-spp', [PembayaranSppController::class, 'index'])->name('pembayaran-spp.index');
    Route::get('/pembayaran-spp/cari-siswa', [PembayaranSppController::class, 'cariSiswa'])->name('pembayaran-spp.cari-siswa');
    Route::post('/pembayaran-spp', [PembayaranSppController::class, 'store'])->name('pembayaran-spp.store');
    Route::get('/setoran-kasir', [SetoranKasirController::class, 'index'])->name('setoran-kasir.index');
    Route::post('/setoran-kasir/tutup-kasir', [SetoranKasirController::class, 'tutupKasir'])->name('setoran-kasir.tutup-kasir');
    Route::get('/dispensasi-spp', [DispensasiSppController::class, 'index'])->name('dispensasi-spp.index');
    Route::post('/dispensasi-spp', [DispensasiSppController::class, 'store'])->name('dispensasi-spp.store');
});

Route::middleware(['auth', 'role:kepala_sekolah'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dispensasi-spp', [DispensasiSppController::class, 'index'])->name('dispensasi-spp.index');
    Route::patch('/dispensasi-spp/{dispensasiSpp}/persetujuan', [DispensasiSppController::class, 'persetujuan'])->name('dispensasi-spp.persetujuan');
    Route::get('/keringanan-spp', [KeringananSppController::class, 'index'])->name('keringanan-spp.index');
    Route::get('/laporan-spp', [TagihanSppController::class, 'index'])->name('laporan-spp.index');
});

Route::middleware(['auth', 'role:komite_sekolah'])->prefix('komite')->name('komite.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/tarif-spp', [TarifSppController::class, 'index'])->name('tarif-spp.index');
    Route::post('/tarif-spp/{tarifSpp}/persetujuan-komite', [TarifSppController::class, 'setujuiKomite'])->name('tarif-spp.setujui-komite');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/tagihan-saya', [TagihanSppController::class, 'index'])->name('tagihan-saya.index');
    Route::get('/dispensasi-saya', [DispensasiSppController::class, 'index'])->name('dispensasi-saya.index');
});

Route::middleware('auth')->prefix('cetak-kuitansi')->name('cetak-kuitansi.')->group(function () {
    Route::get('/{transaksiSpp}/thermal', [CetakKuitansiController::class, 'thermal'])->name('thermal');
    Route::get('/{transaksiSpp}/kuitansi', [CetakKuitansiController::class, 'kuitansi'])->name('kuitansi');
});

require __DIR__.'/auth.php';