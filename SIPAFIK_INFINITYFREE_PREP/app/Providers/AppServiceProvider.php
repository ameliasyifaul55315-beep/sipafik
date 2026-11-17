<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
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
        // Tampilan paginasi bergaya SIPAFIK (bawaan Laravel memerlukan Tailwind yang tidak dimuat).
        Paginator::defaultView('pagination.sipafik');
        Paginator::defaultSimpleView('pagination.sipafik');

        // Nama hari/bulan berbahasa Indonesia untuk translatedFormat().
        Carbon::setLocale('id');

        // Aktifkan di .env produksi: APP_FORCE_HTTPS=true (setelah SSL aktif di hosting).
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }
    }
}
