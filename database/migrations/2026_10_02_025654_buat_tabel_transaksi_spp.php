<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_spp', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kuitansi', 50)->unique(); // Contoh: KW-SPP-202610-0001
            $table->foreignId('tagihan_spp_id')->constrained('tagihan_spp')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('setoran_kasir_id')->nullable()->constrained('setoran_kasir')->nullOnDelete();

            $table->decimal('jumlah_dibayar', 12, 2);
            $table->dateTime('waktu_transaksi');
            $table->enum('metode_pembayaran', ['tunai', 'transfer'])->default('tunai');
            $table->string('nomor_referensi_bank', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_spp');
    }
};
