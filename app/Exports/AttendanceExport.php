<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Permit;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceExport implements FromView, WithColumnWidths, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function view(): View
    {
        // Get all users with their attendance data and permit counts
        $users = User::with(['attendance' => function($query) {
            $query->whereBetween('date_attendance', [$this->startDate, $this->endDate])
                  ->orderBy('date_attendance', 'asc');
        }, 'permits' => function($query) {
            $query->whereBetween('date_permission', [$this->startDate, $this->endDate]);
        }])->get();

        // Generate all days in the month range
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $monthDays = [];
        
        // Create array of all days in the range
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $monthDays[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Process attendance data grouped by user and date
        $userAttendanceData = [];
        foreach ($users as $user) {
            $userAttendanceData[$user->id] = [
                'user' => $user,
                'permit_count' => $user->permits->count(),
                'attendance' => []
            ];
            
            // Initialize all days as absent
            foreach ($monthDays as $day) {
                $userAttendanceData[$user->id]['attendance'][$day] = null;
            }
            
            // Fill in actual attendance data
            foreach ($user->attendance as $attendance) {
                $date = $attendance->date_attendance instanceof Carbon ? 
                        $attendance->date_attendance->format('Y-m-d') : 
                        $attendance->date_attendance;
                $userAttendanceData[$user->id]['attendance'][$date] = $attendance;
            }
        }

        return view('exports.attendance', [
            'users' => $users,
            'userAttendanceData' => $userAttendanceData,
            'monthDays' => $monthDays,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'monthName' => $startDate->format('F Y'),
        ]);
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 6,   // No - balanced size
            'B' => 25,  // Name - adequate for full names
            'C' => 18,  // Department - balanced
            'D' => 18,  // Position - balanced  
            'E' => 12,  // Permit Count - balanced
            'F' => 12,  // GP (Gaji Pokok) - Basic Salary
            'G' => 12,  // TJ (Tunjangan) - Allowance
        ];
        
        // Add width for each day column (balanced for time visibility)
        $columns = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                   'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK'];
        
        for ($i = 0; $i < 31; $i++) { // Maximum 31 days in a month
            if (isset($columns[$i])) {
                $widths[$columns[$i]] = 9; // Balanced columns for time visibility
            }
        }
        
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ]);

        // Set row height for better visibility - balanced sizing
        $highestRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(22); // Balanced height
        }

        // Set header row heights - moderate sizing
        $sheet->getRowDimension(1)->setRowHeight(28); // Main title row
        $sheet->getRowDimension(2)->setRowHeight(32); // Column headers row

        // Set legend row heights - compact but readable
        for ($row = $highestRow - 10; $row <= $highestRow; $row++) {
            if ($row > 0) {
                // Different heights for different legend sections
                if ($row == $highestRow - 4 || $row == $highestRow - 3) {
                    $sheet->getRowDimension($row)->setRowHeight(32); // Legend categories
                } else {
                    $sheet->getRowDimension($row)->setRowHeight(24); // Other legend info
                }
            }
        }

        // Apply text alignment and wrapping
        $sheet->getStyle('A:Z')->applyFromArray([
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrapText' => true
            ],
            'font' => [
                'size' => 10
            ]
        ]);

        // Special formatting for legend section (last 10 rows)
        for ($row = $highestRow - 10; $row <= $highestRow; $row++) {
            if ($row > 0) {
                $sheet->getStyle('A' . $row . ':' . $sheet->getHighestColumn() . $row)->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'bold' => true
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ]
                ]);
            }
        }

        // Apply conditional formatting for attendance status
        $this->applyAttendanceColors($sheet);

        return $sheet;
    }

    private function getAttendanceStatus($timeIn)
    {
        $earlyTime = Carbon::createFromTime(8, 0, 0);     // 08:00 AM - early arrival
        $onTimeEnd = Carbon::createFromTime(8, 30, 0);    // 08:30 AM - end of on time
        $lateEnd = Carbon::createFromTime(9, 0, 0);       // 09:00 AM - end of late
        $veryLateEnd = Carbon::createFromTime(12, 0, 0);  // 12:00 PM - end of very late
        
        if ($timeIn->lte($earlyTime)) {
            return 'Early';
        } elseif ($timeIn->lte($onTimeEnd)) {
            return 'On Time';
        } elseif ($timeIn->lte($lateEnd)) {
            return 'Late';
        } else {
            return 'Very Late'; // Everything after 09:00 is just "Very Late"
        }
    }

    private function getStatusColor($status)
    {
        switch ($status) {
            case 'Early':
                return 'GREEN';
            case 'On Time':
                return 'GREEN';
            case 'Late':
                return 'ORANGE';
            case 'Very Late':
                return 'RED';
            default:
                return 'WHITE';
        }
    }

    private function applyAttendanceColors(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'F'; $col <= $highestColumn; $col++) {
                $cellValue = $sheet->getCell($col . $row)->getValue();
                
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $cellValue)) {
                    $timeIn = Carbon::parse($cellValue);
                    $status = $this->getAttendanceStatus($timeIn);
                    $color = $this->getStatusColor($status);
                    
                    $fillColor = '';
                    switch ($color) {
                        case 'GREEN':
                            $fillColor = 'd4edda'; // Light Green
                            break;
                        case 'ORANGE':
                            $fillColor = 'fff3cd'; // Light Orange
                            break;
                        case 'RED':
                            $fillColor = 'f8d7da'; // Light Red
                            break;
                    }
                    
                    if ($fillColor) {
                        $sheet->getStyle($col . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $fillColor]
                            ]
                        ]);
                    }
                }
            }
        }
    }
}
