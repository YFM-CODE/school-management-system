<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_spp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('tarif_spp_id')->nullable()->constrained('tarif_spp')->nullOnDelete();
            $table->foreignId('keringanan_spp_id')->nullable()->constrained('keringanan_spp')->nullOnDelete();

            $table->unsignedTinyInteger('bulan'); // 1 sampai 12
            $table->year('tahun');

            $table->decimal('nominal_asli', 12, 2);
            $table->decimal('nominal_potongan', 12, 2)->default(0);
            $table->decimal('nominal_tagihan', 12, 2); // nominal_asli - nominal_potongan
            $table->decimal('total_terbayar', 12, 2)->default(0);
            $table->decimal('sisa_tagihan', 12, 2);

            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas'])->default('belum_bayar');
            $table->date('jatuh_tempo')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_spp');
    }
};
