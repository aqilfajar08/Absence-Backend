<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MonthlyAttendanceSheet implements FromView, WithTitle, WithColumnWidths, WithStyles, WithEvents
{
    protected $month;
    protected $year;
    protected $search;
    protected $company;
    protected $daysInMonth;

    public function __construct($month, $year, $search = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->search = $search;
        $this->company = Company::first();
        $this->daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
    }

    public function title(): string
    {
        return strtoupper(Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('M Y'));
    }

    public function view(): View
    {
        $query = User::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Eager load attendance and permits for the specific month
        $query->with(['attendance' => function($q) {
            $q->whereYear('date_attendance', $this->year)
              ->whereMonth('date_attendance', $this->month);
        }, 'permits' => function($q) {
            $q->whereYear('date_permission', $this->year)
              ->whereMonth('date_permission', $this->month)
              ->where('is_approved', 'approved');
        }]);

        $users = $query->get();

        return view('exports.monthly_attendance', [
            'users' => $users,
            'year' => $this->year,
            'month' => $this->month,
            'daysInMonth' => $this->daysInMonth,
            'company' => $this->company
        ]);
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // No
            'B' => 12,  // Dept
            'C' => 18,  // Nama
        ];

        // Columns D onwards are dates (1-31) - each gets width 6
        $startCol = 'D';
        for ($i = 0; $i < $this->daysInMonth; $i++) {
            $col = $this->getColumnLetter(3 + $i); // Starting from column D (index 3)
            $widths[$col] = 6;
        }

        // After dates, we have summary columns
        $summaryStartIndex = 3 + $this->daysInMonth;
        
        // Jumlah group: GPH, TJ, CUTI
        $widths[$this->getColumnLetter($summaryStartIndex)] = 10;     // GPH
        $widths[$this->getColumnLetter($summaryStartIndex + 1)] = 10; // TJ
        $widths[$this->getColumnLetter($summaryStartIndex + 2)] = 6;  // CUTI
        
        // Sub Total group: Cuti, Terlambat, Izin, Sakit, Alfa, Hadir
        for ($i = 3; $i < 9; $i++) {
            $widths[$this->getColumnLetter($summaryStartIndex + $i)] = 8;
        }
        
        // Total group: Cuti, Terlambat, Izin, Sakit, Alfa, Hadir
        for ($i = 9; $i < 15; $i++) {
            $widths[$this->getColumnLetter($summaryStartIndex + $i)] = 8;
        }

        return $widths;
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Row 1: Title - bold and larger
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Row 2: Period - bold
            2 => ['font' => ['bold' => true, 'size' => 12]],
            // Row 3-4: Headers
            3 => ['font' => ['bold' => true, 'size' => 10]],
            4 => ['font' => ['bold' => true, 'size' => 10]],
        ];
    }

    /**
     * Register events for additional formatting
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set default row height
                $sheet->getDefaultRowDimension()->setRowHeight(18);
                
                // Set header rows height
                $sheet->getRowDimension(1)->setRowHeight(25); // Title row
                $sheet->getRowDimension(2)->setRowHeight(22); // Period row
                $sheet->getRowDimension(3)->setRowHeight(20); // Header row 1
                $sheet->getRowDimension(4)->setRowHeight(20); // Header row 2
                
                // Set data rows height (starting from row 5)
                $highestRow = $sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }
                
                // Freeze panes - freeze first 3 columns and first 4 rows
                $sheet->freezePane('D5');
                
                // Set print area and options
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }

    /**
     * Helper function to get column letter from index (0-based)
     */
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