<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthlyAttendanceSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected $month;
    protected $year;
    protected $search;
    protected $company;
    protected $daysInMonth;
    protected $users;
    protected $cellStyles = []; // Store cell styling info

    public function __construct($month, $year, $search = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->search = $search;
        $this->company = Company::first();
        $this->daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        // Load users
        $query = User::query();
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        $query->with(['attendance' => function($q) {
            $q->whereYear('date_attendance', $this->year)
              ->whereMonth('date_attendance', $this->month);
        }, 'permits' => function($q) {
            $q->whereYear('date_permission', $this->year)
              ->whereMonth('date_permission', $this->month)
              ->where('is_approved', 'approved');
        }]);
        
        $this->users = $query->get();
    }

    public function title(): string
    {
        return strtoupper(Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('M Y'));
    }

    public function headings(): array
    {
        $headers = [];
        
        // Row 1: Title
        $totalCols = 3 + $this->daysInMonth + 15;
        $headers[] = ['REKAP ABSENSI KARYAWAN PT. KASAU SINAR SAMUDERA ' . $this->year];
        
        // Row 2: Period
        $headers[] = ['PERIODE ' . strtoupper(Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y'))];
        
        // Row 3: Main Headers
        $row3 = ['No', 'Dept', 'Nama'];
        for ($i = 0; $i < $this->daysInMonth; $i++) {
            $row3[] = ''; // Will be filled with date numbers in row 4
        }
        $row3 = array_merge($row3, ['GPH', 'TJ', 'CUTI', 'Cuti', 'Terlambat', 'Izin', 'Sakit', 'Alfa', 'Hadir']);
        $headers[] = $row3;
        
        // Row 4: Date numbers
        $row4 = ['', '', ''];
        for ($i = 1; $i <= $this->daysInMonth; $i++) {
            $row4[] = $i;
        }
        $row4 = array_merge($row4, array_fill(0, 9, ''));
        $headers[] = $row4;
        
        return $headers;
    }

    public function collection(): Collection
    {
        $data = [];
        
        foreach ($this->users as $index => $user) {
            $row = [];
            $row[] = $index + 1; // No
            $row[] = $user->department ?? '-'; // Dept  
            $row[] = $user->name; // Nama
            
            // Attendance tracking
            $gphCount = 0;
            $tjCount = 0;
            $cutiCount = 0;
            $izinCount = 0;
            $sakitCount = 0;
            $alpaCount = 0;
            $telatCount = 0;
            $hadirCount = 0;
            $late1Count = 0;
            $late2Count = 0;
            $late3Count = 0;
            $late4Count = 0;
            
            // Daily attendance
            for ($i = 1; $i <= $this->daysInMonth; $i++) {
                $currentDate = Carbon::createFromDate($this->year, $this->month, $i, 'Asia/Makassar');
                $dateString = $currentDate->format('Y-m-d');
                $myDay = $currentDate->dayOfWeek;
                $isWeekend = ($myDay == 0 || $myDay == 6); // 0=Sunday, 6=Saturday
                
                $attendance = $user->attendance->first(function($att) use ($dateString) {
                    return Carbon::parse($att->date_attendance)->format('Y-m-d') === $dateString;
                });
                
                $permit = $user->permits->firstWhere('date_permission', $dateString);
                
                $cellContent = '';
                $bgColor = null;
                
                // Logic (same as before)
                if ($permit && $permit->is_approved == 'approved') {
                    if ($permit->permit_type == 'dlk') {
                        $cellContent = 'DLK';
                        $bgColor = '757171';
                        $gphCount += 1;
                        $tjCount += 1;
                        $hadirCount += 1;
                    } elseif ($permit->permit_type == 'sakit') {
                        $cellContent = 'Sakit';
                        $bgColor = 'BFBFBF';
                        $sakitCount++;
                    } elseif ($permit->permit_type == 'cuti') {
                        $cellContent = 'CUTI';
                        $bgColor = '00B050';
                        $cutiCount++;
                    } else {
                        $cellContent = 'IZIN';
                        $bgColor = '757171';
                        $izinCount++;
                    }
                } elseif ($attendance && $attendance->time_in) {
                    $timeIn = Carbon::parse($attendance->time_in)->setDate($this->year, $this->month, $i);
                    $cellContent = $timeIn->format('H:i'); // ALWAYS show time if exists
                    
                    // WEEKEND RULE: Anyone who checks in on weekend is counted as on-time
                    if ($isWeekend) {
                        $bgColor = 'FFFFFF';
                        $gphCount += 1;
                        $tjCount += 1;
                        $hadirCount++;
                    } else {
                        // Regular weekday lateness logic
                        $tTimeIn = Carbon::parse($this->company->time_in ?? '08:00:00')->setDate($this->year, $this->month, $i)->addSeconds(59);
                        $tLate1  = Carbon::parse($this->company->late_threshold_1 ?? '08:30:00')->setDate($this->year, $this->month, $i)->addSeconds(59);
                        $tLate2  = Carbon::parse($this->company->late_threshold_2 ?? '09:00:00')->setDate($this->year, $this->month, $i)->addSeconds(59);
                        $tLate3  = Carbon::parse($this->company->late_threshold_3 ?? '12:00:00')->setDate($this->year, $this->month, $i)->addSeconds(59);
                        
                        if ($timeIn->lte($tTimeIn)) {
                            $bgColor = 'FFFFFF';
                            $gphCount += 1;
                            $tjCount += 1;
                            $hadirCount++;
                        } elseif ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) {
                            $bgColor = '548235';
                            $gphCount += ($this->company->gph_late_1_percent ?? 75) / 100;
                            $tjCount += 1;
                            $telatCount++;
                            $late1Count++; // Increment Late 1
                            $hadirCount++;
                        } elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) {
                            $bgColor = 'FFC000';
                            $gphCount += ($this->company->gph_late_2_percent ?? 70) / 100;
                            $tjCount += 1;
                            $telatCount++;
                            $late2Count++; // Increment Late 2
                            $hadirCount++;
                        } elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) {
                            $bgColor = 'FFFF00';
                            $gphCount += ($this->company->gph_late_3_percent ?? 65) / 100;
                            $tjCount += 1;
                            $telatCount++;
                            $late3Count++; // Increment Late 3
                            $hadirCount++;
                        } else {
                            $bgColor = 'A9D08E';
                            $gphCount += ($this->company->gph_late_4_percent ?? 0) / 100;
                            $tjCount += 0;
                            $telatCount++;
                            $late4Count++; // Increment Late 4
                        }
                    }
                } else {
                    // No attendance record
                    if (!$isWeekend) {
                        // Only mark ALPHA on weekdays
                        if (Carbon::parse($user->created_at)->lte($currentDate)) {
                            if ($currentDate->lte(Carbon::now('Asia/Makassar'))) {
                                $alpaCount++;
                                $cellContent = 'ALPHA';
                                $bgColor = '5B9BD5';
                            }
                        }
                    } else {
                        // Weekend with no attendance: just show red background (empty cell, not ALPHA)
                        $bgColor = 'FF0000';
                    }
                }
                
                $row[] = $cellContent;
                
                // Store style info for this cell
                if ($bgColor) {
                    $colIndex = 2 + $i; // Column D (index 3) for date 1
                    $rowIndex = 5 + $index; // Row 5 onwards (after headers)
                    $this->cellStyles[] = [
                        'row' => $rowIndex,
                        'col' => $colIndex,
                        'bg' => $bgColor,
                    ];
                }
            }
            
            // Calculate GPH (Daily Salary)
            $dailySalary = ($user->gaji_pokok > 0 && $this->daysInMonth > 0) ? ($user->gaji_pokok / $this->daysInMonth) : 0;
            
            // Summary columns
            $row[] = number_format($dailySalary, 0, ',', '.'); // GPH (Displayed as Daily)
            $row[] = number_format($user->tunjangan ?? 0, 0, ',', '.');
            $row[] = $cutiCount;
            $row[] = $cutiCount;
            $row[] = $telatCount;
            $row[] = $izinCount;
            $row[] = $sakitCount;
            $row[] = $alpaCount;
            $row[] = $hadirCount;
            
            $data[] = $row;

            // Store deduction details for later use
            // $dailySalary already calculated above
            
            // Calculate detailed deductions
            $deductions = [
                'late1' => $late1Count * (1 - (($this->company->gph_late_1_percent ?? 75) / 100)) * $dailySalary,
                'late2' => $late2Count * (1 - (($this->company->gph_late_2_percent ?? 70) / 100)) * $dailySalary,
                'late3' => $late3Count * (1 - (($this->company->gph_late_3_percent ?? 65) / 100)) * $dailySalary,
                'late4' => $late4Count * (1 - (($this->company->gph_late_4_percent ?? 0) / 100)) * $dailySalary,
                'izin'  => $izinCount * $dailySalary,
                'sakit' => $sakitCount * $dailySalary, 
                'alpa'  => $alpaCount * $dailySalary,
            ];
            
            $userDeductionData[] = [
                'name' => $user->name,
                'deductions' => $deductions,
                'subtotal1' => array_sum(array_slice($deductions, 0, 4)), // Sum first 4 (Late)
                'subtotal2' => array_sum($deductions), // Sum all (Late + Absence)
            ];
        }
        
        // Add spacer rows
        $data[] = array_fill(0, 3 + $this->daysInMonth + 9, '');
        $data[] = array_fill(0, 3 + $this->daysInMonth + 9, '');
        
        // Legend Config
        $cTimeIn = Carbon::parse($this->company->time_in ?? '08:00:00');
        $cLate1  = Carbon::parse($this->company->late_threshold_1 ?? '08:30:00');
        $cLate2  = Carbon::parse($this->company->late_threshold_2 ?? '09:00:00');
        $cLate3  = Carbon::parse($this->company->late_threshold_3 ?? '12:00:00');
        $range1Start = $cTimeIn->copy()->addMinute()->format('H:i');
        $range4Start = $cLate3->copy()->addMinute()->format('H:i'); // Fixed Range 4 Start

        $legendItems = [
            ['color' => 'FF0000', 'text' => 'Hari Libur (Sabtu & Minggu)'],
            ['color' => '548235', 'text' => 'Terlambat 1 ('.$range1Start.'-'.$cLate1->format('H:i').') [Potong '.(100 - ($this->company->gph_late_1_percent ?? 75)).'%]'],
            ['color' => 'FFC000', 'text' => 'Terlambat 2 ('.$cLate1->copy()->addMinute()->format('H:i').'-'.$cLate2->format('H:i').') [Potong '.(100 - ($this->company->gph_late_2_percent ?? 70)).'%]'],
            ['color' => 'FFFF00', 'text' => 'Terlambat 3 ('.$cLate2->copy()->addMinute()->format('H:i').'-'.$cLate3->format('H:i').') [Potong '.(100 - ($this->company->gph_late_3_percent ?? 65)).'%]'],
            ['color' => 'A9D08E', 'text' => 'Setengah Hari (>'.$range4Start.') [Potong '.(100 - ($this->company->gph_late_4_percent ?? 0)).'%]'],
            ['color' => '757171', 'text' => 'Dinas Luar Kota (DLK) / Izin'],
            ['color' => 'BFBFBF', 'text' => 'Sakit'],
            ['color' => '00B050', 'text' => 'Off/Cuti'],
            ['color' => '5B9BD5', 'text' => 'Alpa (Tanpa Keterangan)'],
        ];
        
        // Deduction Table Headers
        $deductionHeader1 = ['No', 'Nama', 'Terlambat', '', '', '', 'Sub Total I', 'Izin/Sakit/Alfa', '', '', 'Sub Total II', 'Uniform Ded.', 'Total Deduction'];
        $deductionHeader2 = ['', '', 'I', 'II', 'III', 'IV', '', 'Izin', 'Sakit', 'Alfa', '', '', ''];
        
        // Start Row for Styles (relative to current data length + 1 + 4 headers)
        // Data starts at Row 5.
        // If count($data) is N. Next row index is N. Excel row is N + 5.
        $startRow = count($data) + 5; 
        
        // Calculate max rows needed (Legend vs Users + 2 Headers)
        $totalDeductionRows = 2 + count($userDeductionData);
        $totalLegendRows = 1 + count($legendItems); // +1 for "Keterangan:" header
        $maxRows = max($totalLegendRows, $totalDeductionRows);
        
        for ($i = 0; $i < $maxRows; $i++) {
            $row = [];
            
            // --- LEFT SIDE: LEGEND ---
            if ($i == 0) {
                // Legend Header
                $row[] = '';
                $row[] = 'Keterangan:';
            } elseif ($i - 1 < count($legendItems)) {
                // Legend Item
                $legend = $legendItems[$i - 1];
                $row[] = ''; // Color box
                $row[] = $legend['text'];
                
                // Style for Color Box
                $currentRow = $startRow + $i;
                $this->cellStyles[] = [
                    'row' => $currentRow,
                    'col' => 0, // A
                    'bg' => $legend['color'],
                ];
            } else {
                // Empty Legend Space
                $row[] = '';
                $row[] = '';
            }
            
            // --- MIDDLE: GAP ---
            $row[] = '';
            $row[] = ''; 
            
            // --- RIGHT SIDE: DEDUCTION TABLE (Starts at Col E / Index 4) ---
            if ($i == 0) {
                // Header 1: Expand headers to occupy 2 cols each
                $rawHeaders = ['No', 'Nama', 'Terlambat', '', '', '', 'Sub Total I', 'Izin/Sakit/Alfa', '', '', 'Sub Total II', 'Uniform Ded.', 'Total Deduction'];
                // Actually need to construct careful spacing for merged headers
                // Items: No, Nama, Late(4 items), Sub1, Abs(3 items), Sub2, Uni, Tot. Total 13 items.
                // We want each "Item" to take 2 cols.
                // So "No" takes 2, "Nama" takes 2.
                // "Terlambat" covers 4 items * 2 = 8 cols.
                $row = array_merge($row, ['No', '', 'Nama', '', 'Terlambat', '', '', '', '', '', '', '', 'Sub Total I', '', 'Izin/Sakit/Alfa', '', '', '', '', '', 'Sub Total II', '', 'Uniform Ded.', '', 'Total Deduction', '']);
                
            } elseif ($i == 1) {
                // Header 2: I, II ...
                // Each sub-header takes 2 cols
                $dRow = [];
                // No, Nama (Empty in row 2)
                $dRow[] = ''; $dRow[] = ''; // No
                $dRow[] = ''; $dRow[] = ''; // Nama
                $dRow[] = 'I'; $dRow[] = '';
                $dRow[] = 'II'; $dRow[] = '';
                $dRow[] = 'III'; $dRow[] = '';
                $dRow[] = 'IV'; $dRow[] = '';
                $dRow[] = ''; $dRow[] = ''; // Sub1
                $dRow[] = 'Izin'; $dRow[] = '';
                $dRow[] = 'Sakit'; $dRow[] = '';
                $dRow[] = 'Alfa'; $dRow[] = '';
                $dRow[] = ''; $dRow[] = ''; // Sub2
                $dRow[] = ''; $dRow[] = ''; // Uni
                $dRow[] = ''; $dRow[] = ''; // Total
                
                $row = array_merge($row, $dRow);
                
                // Style Header 2 colors (Apply to pairs)
                // Col Indices (relative to row, start index 4)
                // Item 3 (Late1) starts at index 4 + 4 = 8.
                $baseIdx = 4;
                $colorMap = [
                    8 => '548235',  // Late 1 (I, J)
                    10 => 'FFC000', // Late 2 (K, L)
                    12 => 'FFFF00', // Late 3 (M, N)
                    14 => 'A9D08E', // Late 4 (O, P)
                    18 => '757171', // Izin (S, T)
                    20 => 'BFBFBF', // Sakit (U, V)
                    22 => '5B9BD5', // Alfa (W, X)
                ];
                foreach ($colorMap as $cIdx => $hex) {
                    $this->cellStyles[] = ['row' => $startRow + $i, 'col' => $cIdx, 'bg' => $hex];     // Left
                    $this->cellStyles[] = ['row' => $startRow + $i, 'col' => $cIdx + 1, 'bg' => $hex]; // Right
                }
                
            } elseif ($i - 2 < count($userDeductionData)) {
                $userData = $userDeductionData[$i - 2];
                $dRow = [];
                $fmt = fn($val) => $val > 0 ? number_format($val, 0, ',', '.') : '-';
                
                // Helper to push double
                $push = function($val) use (&$dRow) {
                    $dRow[] = $val;
                    $dRow[] = '';
                };
                
                $push($i - 1); // No
                $push($userData['name']); // Nama
                $push($fmt($userData['deductions']['late1']));
                $push($fmt($userData['deductions']['late2']));
                $push($fmt($userData['deductions']['late3']));
                $push($fmt($userData['deductions']['late4']));
                $push($fmt($userData['subtotal1'])); 
                $push($fmt($userData['deductions']['izin']));
                $push($fmt($userData['deductions']['sakit']));
                $push($fmt($userData['deductions']['alpa']));
                $push($fmt($userData['subtotal2'])); 
                $push('-'); 
                $push($fmt($userData['subtotal2'])); 
                
                $row = array_merge($row, $dRow);
                
                // Apply background colors to data (Pairs)
                $currentRow = $startRow + $i;
                // Indices relative to full row (Gap=4).
                // Deduction start at 4.
                // Late 1 at 4+4 = 8.
                $stripeColors = [
                    8 => '548235', 10 => 'FFC000', 12 => 'FFFF00', 14 => 'A9D08E', // Lates
                    18 => '757171', 20 => 'BFBFBF', 22 => '5B9BD5', // Absence
                    16 => 'D9D9D9', 24 => 'D9D9D9', 26 => 'FCE4D6', 28 => 'FFFF00' // Subs & Total
                ];
                 foreach ($stripeColors as $cIdx => $hex) {
                    $this->cellStyles[] = ['row' => $currentRow, 'col' => $cIdx, 'bg' => $hex];
                    $this->cellStyles[] = ['row' => $currentRow, 'col' => $cIdx + 1, 'bg' => $hex];
                }
            }
            
            $data[] = $row;
        }
        
        return collect($data);
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,
            'B' => 12,
            'C' => 18,
        ];
        
        for ($i = 0; $i < $this->daysInMonth; $i++) {
            $col = $this->getColumnLetter(3 + $i);
            $widths[$col] = 6;
        }
        
        $summaryStart = 3 + $this->daysInMonth;
        for ($i = 0; $i < 9; $i++) {
            $widths[$this->getColumnLetter($summaryStart + $i)] = 10;
        }
        
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate total columns
        $totalCols = 3 + $this->daysInMonth + 9;
        $lastCol = $this->getColumnLetter($totalCols - 1);
        $lastRow = 4 + count($this->users);
        
        // Apply all borders to data range
        $dataRange = 'A3:' . $lastCol . $lastRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // Apply stored cell background colors
        foreach ($this->cellStyles as $style) {
            $cellCoord = $this->getColumnLetter($style['col']) . $style['row'];
            $sheet->getStyle($cellCoord)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF' . $style['bg']);
        }
        
        // Header styling
        $sheet->getStyle('1:4')->getFont()->setBold(true);
        
        // Header background colors
        $headerRange = 'A3:' . $lastCol . '4';
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9D9D9');
        
        // Title rows - merge and center
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setSize(14)->setBold(true);
        
        $this->styleDeductionTable($sheet);
        
        return [];
    }
    
    // Extracted Deduction Table Styling
    private function styleDeductionTable(Worksheet $sheet) {
        $dStartRow = count($this->users) + 7;
        
        // --- MERGE HEADERS ---
        // Top Group Headers
        $sheet->mergeCells("I{$dStartRow}:P{$dStartRow}"); // Terlambat (Late 1-4 = 8 cols)
        $sheet->mergeCells("S{$dStartRow}:X{$dStartRow}"); // Izin/Sakit/Alfa (3 items = 6 cols)
        
        // Vertical Merges + Horizontal Merges for Main Columns
        // No (Item 1) -> E, F
        $sheet->mergeCells("E{$dStartRow}:F" . ($dStartRow + 1));
        // Nama (Item 2) -> G, H
        $sheet->mergeCells("G{$dStartRow}:H" . ($dStartRow + 1));
        // Sub1 (Item 7) -> Q, R
        $sheet->mergeCells("Q{$dStartRow}:R" . ($dStartRow + 1));
        // Sub2 (Item 11) -> Y, Z
        $sheet->mergeCells("Y{$dStartRow}:Z" . ($dStartRow + 1));
        // Uniform (Item 12) -> AA, AB
        $sheet->mergeCells("AA{$dStartRow}:AB" . ($dStartRow + 1));
        // Total (Item 13) -> AC, AD
        $sheet->mergeCells("AC{$dStartRow}:AD" . ($dStartRow + 1));
        
        // Sub-Headers (Row 2) - Merge Pairs
        $h2 = $dStartRow + 1;
        $sheet->mergeCells("I{$h2}:J{$h2}"); // Late I
        $sheet->mergeCells("K{$h2}:L{$h2}"); // Late II
        $sheet->mergeCells("M{$h2}:N{$h2}"); // Late III
        $sheet->mergeCells("O{$h2}:P{$h2}"); // Late IV
        
        $sheet->mergeCells("S{$h2}:T{$h2}"); // Izin
        $sheet->mergeCells("U{$h2}:V{$h2}"); // Sakit
        $sheet->mergeCells("W{$h2}:X{$h2}"); // Alfa
        
        // --- STYLES ---
        // Header Text Styles
        $sheet->getStyle("E{$dStartRow}:AD" . ($dStartRow + 1))->getFont()->setBold(true);
        $sheet->getStyle("E{$dStartRow}:AD" . ($dStartRow + 1))->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
            
        // Header Backgrounds Row 1 (Groups)
        $sheet->getStyle("I{$dStartRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        $sheet->getStyle("S{$dStartRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
        
        // Header Backgrounds Row 2 (Sub-headers)
        $colors = [
            'I' => '548235', 'K' => 'FFC000', 'M' => 'FFFF00', 'O' => 'A9D08E', // Lates
            'S' => '757171', 'U' => 'BFBFBF', 'W' => '5B9BD5', // Absence
        ];
        foreach ($colors as $col => $argb) {
            $sheet->getStyle($col . $h2)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($argb);
        }
        
        // --- DATA MERGES (Loop through users) ---
        $dataStart = $dStartRow + 2;
        $userCount = count($this->users);
        $dEndRow = $dataStart + $userCount - 1;
        
        // Column Pairs to Merge:
        // No(E-F), Nama(G-H), I(I-J), II(K-L), III(M-N), IV(O-P), Sub1(Q-R), Izin(S-T), Sakit(U-V), Alfa(W-X), Sub2(Y-Z), Uni(AA-AB), Tot(AC-AD)
        $mergePairs = ['E'=>'F', 'G'=>'H', 'I'=>'J', 'K'=>'L', 'M'=>'N', 'O'=>'P', 'Q'=>'R', 'S'=>'T', 'U'=>'V', 'W'=>'X', 'Y'=>'Z', 'AA'=>'AB', 'AC'=>'AD'];
        
        for ($r = $dataStart; $r <= $dEndRow; $r++) {
            foreach ($mergePairs as $c1 => $c2) {
                $sheet->mergeCells("{$c1}{$r}:{$c2}{$r}");
            }
        }
        
        // Borders (Whole Table E -> AD)
        $sheet->getStyle("E{$dStartRow}:AD{$dEndRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ],
            ],
        ]);
        
        // Align Numbers Right (Data Rows)
        if ($dEndRow >= $dataStart) {
            $sheet->getStyle("I{$dataStart}:AD{$dEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            // Center 'No'
            $sheet->getStyle("E{$dataStart}:F{$dEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function getColumnLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = floor($index / 26) - 1;
        }
        return $letter;
    }
}