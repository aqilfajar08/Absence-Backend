<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetupNotificationTest extends Command
{
    protected $signature = 'test:notification-setup';
    protected $description = 'Setup test data for notification testing';

    public function handle()
    {
        $this->info('Setting up notification test data...');
        
        // 1. Use first available user (any user)
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found in database!');
            $this->info('Please add at least one user to the database.');
            return;
        }

        // 2. Set dummy FCM token
        $user->fcm_token = 'dummy_token_test_' . now()->timestamp;
        $user->save();
        $this->info("✓ FCM token set for: {$user->name}");

        // 3. Create attendance record (check-in only, no checkout)
        $today = Carbon::today()->toDateString();
        
        // Delete existing attendance for today to avoid duplicates
        Attendance::where('user_id', $user->id)
            ->where('date_attendance', $today)
            ->delete();

        Attendance::create([
            'user_id' => $user->id,
            'date_attendance' => $today,
            'time_in' => '08:00:00',
            'latlon_in' => '-1.150312,116.881607',
        ]);
        
        $this->info("✓ Attendance record created for today (checked in, not checked out)");
        
        // 4. Show summary
        $this->newLine();
        $this->info('=== Test Data Ready ===');
        $this->table(
            ['Field', 'Value'],
            [
                ['User ID', $user->id],
                ['User Name', $user->name],
                ['User Email', $user->email],
                ['FCM Token', substr($user->fcm_token, 0, 30) . '...'],
                ['Date', $today],
                ['Check-In Time', '08:00:00'],
                ['Check-Out Time', 'NULL (belum checkout)'],
            ]
        );

        // 5. Verify the data was inserted
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date_attendance', $today)
            ->first();
        
        if ($attendance) {
            $this->info("✓ Verified: Attendance record ID {$attendance->id} exists in database");
        } else {
            $this->error("✗ Warning: Could not verify attendance record!");
        }

        $this->newLine();
        $this->info('Now run: php artisan checkout:remind before');
        $this->info('Or run: php artisan checkout:remind now');
    }
}
