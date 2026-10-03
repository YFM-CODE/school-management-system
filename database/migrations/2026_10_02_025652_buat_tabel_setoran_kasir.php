<?php

// use App\Enums\StatusSetoranKasir;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_kasir', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_setoran', 50)->unique(); // Contoh: STR-20261002-001
            $table->foreignId('staf_tu_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('bendahara_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_setoran');
            $table->decimal('total_sistem', 12, 2);
            $table->decimal('total_fisik', 12, 2)->nullable();
            $table->decimal('selisih', 12, 2)->default(0);
            $table->enum('status', ['menunggu_verifikasi', 'diterima', 'ditolak'])->default('menunggu_verifikasi');
            $table->text('catatan_bendahara')->nullable();
            $table->dateTime('waktu_verifikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_kasir');
    }
};
