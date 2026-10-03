<?php

namespace App\Providers;

use App\Interfaces\DispensasiSppInterface;
use App\Interfaces\KeringananSppInterface;
use App\Interfaces\SetoranKasirInterface;
use App\Interfaces\TagihanSppInterface;
use App\Interfaces\TarifSppInterface;
use App\Interfaces\TransaksiSppInterface;
use App\Repositories\DispensasiSppRepository;
use App\Repositories\KeringananSppRepository;
use App\Repositories\SetoranKasirRepository;
use App\Repositories\TagihanSppRepository;
use App\Repositories\TarifSppRepository;
use App\Repositories\TransaksiSppRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TarifSppInterface::class, TarifSppRepository::class);
        $this->app->bind(KeringananSppInterface::class, KeringananSppRepository::class);
        $this->app->bind(TagihanSppInterface::class, TagihanSppRepository::class);
        $this->app->bind(TransaksiSppInterface::class, TransaksiSppRepository::class);
        $this->app->bind(SetoranKasirInterface::class, SetoranKasirRepository::class);
        $this->app->bind(DispensasiSppInterface::class, DispensasiSppRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vite::prefetch(concurrency: 3);
    }
}
