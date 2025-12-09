<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Pengumuman;
use App\Models\User;
use App\Observers\AbsensiObserver;
use App\Observers\HariLiburObserver;
use App\Observers\PengumumanObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Absensi::observe(AbsensiObserver::class);
        User::observe(UserObserver::class);
        HariLibur::observe(HariLiburObserver::class);
        Pengumuman::observe(PengumumanObserver::class);
    }
}
