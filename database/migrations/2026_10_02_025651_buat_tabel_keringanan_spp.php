<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Jenis Keringanan
        Schema::create('keringanan_spp', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program', 100); // Contoh: Beasiswa Prestasi, Bantuan SKTM, Anak Pegawai
            $table->enum('tipe_potongan', ['nominal', 'persentase'])->default('nominal');
            $table->decimal('nilai_potongan', 12, 2); // Nilai nominal atau angka persen
            $table->text('syarat_ketentuan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 2. Penetapan SK Siswa Penerima Keringanan
        Schema::create('siswa_keringanan_spp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('keringanan_spp_id')->constrained('keringanan_spp')->cascadeOnDelete();
            $table->string('tahun_ajaran', 9);
            $table->string('nomor_sk', 100)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_keringanan_spp');
        Schema::dropIfExists('keringanan_spp');
    }
};
