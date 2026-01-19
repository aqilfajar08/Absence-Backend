<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ResetAttendanceTodaySeeder extends Seeder
{
    public function run(): void
    {
        // Cari absensi hari ini (berdasarkan waktu server / 2026-01-14 sesuai konteks user)
        // Kita pakai Carbon::now() agar dinamis sesuai jam server
        $today = Carbon::now('Asia/Makassar')->toDateString();
        
        $deleted = Attendance::where('date_attendance', $today)->delete();
        
        $this->command->info("Deleted $deleted attendance records for today ($today).");
    }
}
