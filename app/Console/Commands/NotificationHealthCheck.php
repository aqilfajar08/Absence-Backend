<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class NotificationHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Health check for checkout notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Notification System Health Check...');
        $this->newLine();

        $hasError = false;

        // 1. Check Firebase Credentials File
        $path = storage_path('app/firebase-auth.json');
        $envPath = config('firebase.projects.app.credentials.file') ?? env('FIREBASE_CREDENTIALS');
        
        $this->line('1. Firebase Credentials File:');
        if (File::exists($path) || ($envPath && File::exists(base_path($envPath)))) {
            $this->info("   ✓ Found at: " . ($envPath ? $envPath : 'storage/app/firebase-auth.json'));
        } else {
            $this->error("   ✗ MISSING! File not found at storage/app/firebase-auth.json");
            $this->line("     Action: Upload your firebase-admin-sdk.json to storage/app/firebase-auth.json");
            $hasError = true;
        }

        // 2. Check Database Connection & Company Settings
        // Use try-catch to prevent console failure if database is not ready
        try {
            $this->newLine();
            $this->line('2. Company Settings:');
            
            // Check connection first
            DB::connection()->getPdo();
            
            $company = Company::first();
            if ($company) {
                $this->info("   ✓ Company found: {$company->name}");
                $this->info("   ✓ Checkout Time: " . ($company->time_out ?? 'NOT SET'));
                
                if (!$company->time_out) {
                    $this->error("     ✗ Warning: time_out is null. Notifications won't run.");
                    $hasError = true;
                }
            } else {
                $this->error("   ✗ No Company Data found in database.");
                $hasError = true;
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Database Error: " . $e->getMessage());
            $hasError = true;
        }

        // 3. Check FCM Tokens
        try {
             $this->newLine();
             $this->line('3. User Data (FCM Tokens):');
             $userCount = User::count();
             $tokenCount = User::whereNotNull('fcm_token')->count();
             
             if ($userCount > 0) {
                 $this->info("   ✓ Total Users: {$userCount}");
                 if ($tokenCount > 0) {
                     $this->info("   ✓ Users with FCM Token: {$tokenCount}");
                 } else {
                     $this->warn("   ! No users have FCM tokens yet. No notifications will be sent.");
                     $this->line("     Action: Users must login via App to update tokens.");
                 }
             } else {
                 $this->error("   ✗ No users in database.");
             }
        } catch (\Exception $e) {
             $this->error("   ✗ Database Error: " . $e->getMessage());
        }

        // 4. Check Scheduler Configuration
        $this->newLine();
        $this->line('4. System Configuration:');
        $timezone = config('app.timezone');
        $this->info("   ✓ Timezone: {$timezone}");
        
        $this->newLine();
        if ($hasError) {
            $this->error('❌ System has issues. Please fix above errors.');
        } else {
            $this->info('✅ System looks good! You are ready to deploy.');
            $this->line('Run: "php artisan checkout:remind before" to test a manual trigger.');
        }
    }
}
