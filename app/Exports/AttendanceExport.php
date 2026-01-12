<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\MonthlyAttendanceSheet;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceExport implements WithMultipleSheets
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

        public function sheets(): array
    {
        $sheets = [];

        if (!empty($this->request->start_date) && !empty($this->request->end_date)) {
            $startDate = Carbon::parse($this->request->start_date)->startOfMonth();
            $endDate = Carbon::parse($this->request->end_date)->endOfMonth();
        } else {
            $firstAttendance = Attendance::min('date_attendance');
            $startDate = $firstAttendance ? Carbon::parse($firstAttendance)->startOfMonth() : Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            // 1. Sheet Rekap Gaji (yang tadi Anda buat)
            // Ubah judul di MonthlyAttendanceSheet.php jadi 'Rekap ...' biar jelas
            $sheets[] = new MonthlyAttendanceSheet(
                $current->month, 
                $current->year, 
                $this->request->search
            );
            
            // 2. Sheet Detail Harian (yang baru kita buat)
            $sheets[] = new \App\Exports\Sheets\DailyAttendanceSheet(
                $current->month, 
                $current->year, 
                $this->request->search
            );
            
            $current->addMonth();
        }

        return $sheets;
    }
}