<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyAttendanceSeeder extends Seeder
{
    public function run()
    {
        // Ambil users selain admin
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })->get();

        if ($users->isEmpty()) {
            $this->command->info('No users found to seed attendance.');
            return;
        }

        $dates = ['2026-01-16', '2026-01-17'];

        foreach ($dates as $date) {
            foreach ($users as $index => $user) {
                // Variasi jam masuk agar status beragam
                // 0 = Tepat Waktu (07:30 - 07:55)
                // 1 = Terlambat 1 (08:01 - 08:15)
                // 2 = Terlambat 2 (08:16 - 08:30)
                // 3 = Terlambat 3 (08:31 - 08:45)
                // 4 = Setengah Hari (> 08:45)
                
                $scenario = $index % 5; 
                $jamMasuk = '07:30:00';
                $isLate = false;
                $note = null;

                switch ($scenario) {
                    case 0: // Tepat Waktu
                        $jamMasuk = '07:' . rand(45, 55) . ':00';
                        $isLate = false;
                        break;
                    case 1: // Terlambat 1
                        $jamMasuk = '08:05:00';
                        $isLate = true;
                        $note = 'Macet di jalan urip';
                        break;
                    case 2: // Terlambat 2
                        $jamMasuk = '08:20:00';
                        $isLate = true;
                        $note = 'Ban bocor';
                        break;
                    case 3: // Terlambat 3
                        $jamMasuk = '08:40:00';
                        $isLate = true;
                        $note = 'Ada urusan keluarga';
                        break;
                    case 4: // Setengah Hari
                        $jamMasuk = '10:00:00';
                        $isLate = true;
                        $note = 'Izin terlambat datang';
                        break;
                }

                // Cek apakah data sudah ada
                $exists = Attendance::where('user_id', $user->id)
                    ->where('date_attendance', $date)
                    ->exists();

                if (!$exists) {
                    Attendance::create([
                        'user_id' => $user->id,
                        'date_attendance' => $date,
                        'time_in' => $jamMasuk,
                        'time_out' => ($date == '2026-01-16' && $scenario != 4) ? '17:05:00' : null, // Sebagian sudah pulang
                        'latlon_in' => '-5.147665,119.432731',
                        'latlon_out' => '-5.147665,119.432731',
                        'is_late' => $isLate,
                        'note' => $note
                    ]);
                }
            }
        }
        
        $this->command->info('Dummy attendance data for Jan 16 & 17 seeded successfully!');
    }
}
