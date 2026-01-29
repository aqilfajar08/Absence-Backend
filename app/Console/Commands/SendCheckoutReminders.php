<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class SendCheckoutReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:remind {type=before}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send checkout reminders to employees (before = 10 min before, now = at checkout time)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        // Get company settings
        $company = Company::first();
        if (!$company) {
            $this->error('No company settings found!');
            Log::error('SendCheckoutReminders: No company found');
            return;
        }

        // Check for Firebase credentials file existence to prevent crash
        $credentialsPath = config('firebase.projects.app.credentials.file') ?? env('FIREBASE_CREDENTIALS');
        
        // If path is relative, make it absolute relative to base path
        if ($credentialsPath && !file_exists($credentialsPath) && !file_exists(base_path($credentialsPath))) {
             // Check standard storage path
             if (!file_exists(storage_path('app/firebase-auth.json'))) {
                $this->error('Firebase credentials file not found! Please check storage/app/firebase-auth.json');
                Log::error('SendCheckoutReminders: Firebase credentials missing');
                return;
             }
        }

        $timeOut = $company->time_out; // e.g., "17:00"
        $today = Carbon::today()->toDateString();

        $this->info("Checking for today: {$today}");
        $this->info("Company checkout time: {$timeOut}");

        // Get employees who have checked in but not checked out yet
        $employeesNeedingCheckout = Attendance::where('date_attendance', $today)
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->pluck('user_id')
            ->unique();

        $this->info("Found {$employeesNeedingCheckout->count()} employees who checked in but not checked out");

        if ($employeesNeedingCheckout->isEmpty()) {
            $this->info('No employees need checkout reminder.');
            return;
        }

        // Get users with FCM tokens
        $users = User::whereIn('id', $employeesNeedingCheckout)
            ->whereNotNull('fcm_token')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No employees with FCM tokens to send notification.');
            return;
        }

        // Determine message based on type
        if ($type === 'before') {
            $message = '10 menit lagi pulang, Jangan lupa melakukan absen pulang!';
            $this->info('Sending 10-minute reminder to ' . $users->count() . ' employees...');
        } else {
            $message = 'Waktunya pulang, Jangan lupa melakukan absen pulang!';
            $this->info('Sending checkout time reminder to ' . $users->count() . ' employees...');
        }

        $successCount = 0;
        $failCount = 0;

        // Send notifications
        foreach ($users as $user) {
            try {
                $this->sendNotification($user->fcm_token, $message);
                $successCount++;
                $this->info("✓ Sent to: {$user->name}");
            } catch (\Throwable $e) {
                $failCount++;
                $this->error("✗ Failed for: {$user->name} - {$e->getMessage()}");
                Log::error('SendCheckoutReminders failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Completed! Success: {$successCount}, Failed: {$failCount}");
        Log::info('SendCheckoutReminders completed', [
            'type' => $type,
            'success' => $successCount,
            'failed' => $failCount,
        ]);
    }

    /**
     * Send FCM notification to a user
     */
    private function sendNotification($fcmToken, $message)
    {
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Pengingat Absen Pulang', $message);

        $cloudMessage = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification($notification);

        $messaging->send($cloudMessage);
    }
}
