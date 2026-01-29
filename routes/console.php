<?php

use App\Models\Company;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Scheduled Checkout Reminders
// These tasks will send notifications to employees who haven't checked out yet

// Get company checkout time dynamically
// Use try-catch to prevent console failure if database is not ready
try {
    $company = Company::first();

    if ($company && $company->time_out) {
        // Parse time_out (e.g., "17:00")
        $timeOutParts = explode(':', $company->time_out);
        $hourOut = (int) $timeOutParts[0];
        $minuteOut = (int) $timeOutParts[1];

        // Calculate 10 minutes before checkout
        $reminderTime = sprintf('%02d:%02d', $hourOut, max(0, $minuteOut - 10));
        if ($minuteOut < 10) {
            // If minutes < 10, need to go back an hour
            $reminderHour = $hourOut - 1;
            $reminderMinute = 60 + $minuteOut - 10;
            $reminderTime = sprintf('%02d:%02d', $reminderHour, $reminderMinute);
        }

        /*
        // MENONAKTIFKAN SCHEDULER OTOMATIS (Sesuai Request: Agar tidak berat di server teman)
        // Jika ingin diaktifkan kembali, hilangkan komentar (/* ... * /) ini.

        // Schedule: 10 minutes before checkout time
        Schedule::command('checkout:remind before')
            ->dailyAt($reminderTime)
            ->timezone(config('app.timezone', 'Asia/Makassar'))
            ->onSuccess(function () {
                info('✓ 10-minute checkout reminder sent successfully');
            })
            ->onFailure(function () {
                info('✗ 10-minute checkout reminder failed');
            });

        // Schedule: At checkout time
        Schedule::command('checkout:remind now')
            ->dailyAt($company->time_out)
            ->timezone(config('app.timezone', 'Asia/Makassar'))
            ->onSuccess(function () {
                info('✓ Checkout time reminder sent successfully');
            })
            ->onFailure(function () {
                info('✗ Checkout time reminder failed');
            });
        */
    }
} catch (\Throwable $e) {
    // Fail silently if DB connection fails (e.g. during build/deploy)
}
