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
        // Force PHP timezone to Asia/Makassar (WITA)
        // This ensures all date() and time() functions use the correct timezone
        // regardless of server OS timezone settings
        date_default_timezone_set('Asia/Makassar');
        
        Paginator::useBootstrapFive();
        \Carbon\Carbon::setLocale('id');

        // Fix CSS/Assets hilang saat pakai Tunneling Service (Ngrok, Cloudflare, LocalTunnel)
        // Kita paksa URL Root agar menggunakan domain tunnel, bukan IP lokal
        $tunnelDomains = ['ngrok', 'trycloudflare.com', 'loca.lt'];
        
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            foreach ($tunnelDomains as $domain) {
                if (str_contains($_SERVER['HTTP_X_FORWARDED_HOST'], $domain)) {
                    $url = 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
                    \Illuminate\Support\Facades\URL::forceRootUrl($url);
                    \Illuminate\Support\Facades\URL::forceScheme('https');
                    break;
                }
            }
        }
        
        // Fallback: Deteksi dari URL request
        foreach ($tunnelDomains as $domain) {
            if (str_contains(request()->url(), $domain)) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
                break;
            }
        }
    }
}
