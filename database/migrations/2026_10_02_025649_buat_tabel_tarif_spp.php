<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_spp', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 9); // Contoh: 2026/2027
            $table->string('tingkat', 10);     // Contoh: X, XI, XII
            $table->decimal('nominal_standar', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tahun_ajaran', 'tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_spp');
    }
};
