<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();
        \Carbon\Carbon::setLocale('id');

        // Fix CSS/Assets hilang saat pakai Ngrok
        // Kita paksa URL Root agar menggunakan domain Ngrok, bukan IP lokal (127.0.0.1/192.168.x.x)
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && str_contains($_SERVER['HTTP_X_FORWARDED_HOST'], 'ngrok')) {
            $url = 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
            \Illuminate\Support\Facades\URL::forceRootUrl($url);
            \Illuminate\Support\Facades\URL::forceScheme('https');
        } elseif (str_contains(request()->url(), 'ngrok-free.app')) {
            // Fallback jika header tidak terbaca tapi URL terdeteksi
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
