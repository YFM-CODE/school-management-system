<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persetujuan_tarif_komite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarif_spp_id')->constrained('tarif_spp')->cascadeOnDelete();
            $table->foreignId('ketua_komite_id')->constrained('users')->restrictOnDelete();
            $table->string('nomor_berita_acara', 100);
            $table->date('tanggal_kesepakatan');
            $table->string('dokumen_ba_pdf')->nullable();
            $table->enum('status_persetujuan', ['draft', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_komite')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persetujuan_tarif_komite');
    }
};
