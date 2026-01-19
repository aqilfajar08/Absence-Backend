<?php

namespace App\Exports\Sheets;

use App\Models\Attendance;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class DailyAttendanceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $month;
    protected $year;
    protected $search;
    protected $company;

    public function __construct($month, $year, $search = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->search = $search;
        $this->company = Company::first();
    }

    public function title(): string
    {
        return 'Detail ' . Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('M Y');
    }

    public function collection()
    {
        $query = Attendance::with('user')
            ->whereYear('date_attendance', $this->year)
            ->whereMonth('date_attendance', $this->month);

        if ($this->search) {
            $searchTerm = $this->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('date_attendance', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Keterangan',
            'Denda Harian'
        ];
    }

    public function map($attendance): array
    {
        $keterangan = '-';
        $denda = 0;
        $statusLabel = '-';

        if ($attendance->time_in) {
            $timeIn = Carbon::parse($attendance->time_in);
            
            // Thresholds (Matching Web Logic)
            // Use time_in from company or default 08:00:00
            $tTimeIn = Carbon::parse($this->company->time_in ?? '08:00:00')->setDateFrom($timeIn)->addSeconds(59);
            $tLate1  = Carbon::parse($this->company->late_threshold_1 ?? '08:30:00')->setDateFrom($timeIn)->addSeconds(59);
            $tLate2  = Carbon::parse($this->company->late_threshold_2 ?? '09:00:00')->setDateFrom($timeIn)->addSeconds(59);
            $tLate3  = Carbon::parse($this->company->late_threshold_3 ?? '12:00:00')->setDateFrom($timeIn)->addSeconds(59);

            if ($timeIn->lte($tTimeIn)) {
                $statusLabel = 'Tepat Waktu';
            } elseif ($timeIn->gt($tTimeIn) && $timeIn->lte($tLate1)) {
                 $statusLabel = 'Terlambat 1';
            } elseif ($timeIn->gt($tLate1) && $timeIn->lte($tLate2)) {
                 $statusLabel = 'Terlambat 2';
            } elseif ($timeIn->gt($tLate2) && $timeIn->lte($tLate3)) {
                 $statusLabel = 'Terlambat 3';
            } else {
                 $statusLabel = 'Setengah Hari';
            }
            
            // Calculate Keterangan (Minutes Late) and Denda if Late
            if ($statusLabel !== 'Tepat Waktu') {
                 // Recalculate base time without tolerance for minute calculation
                 $baseTimeIn = Carbon::parse($this->company->time_in ?? '08:00:00')->setDateFrom($timeIn);
                 
                 if ($timeIn->gt($baseTimeIn)) {
                     $minutesLate = abs($timeIn->diffInMinutes($baseTimeIn));
                     $keterangan = "Terlambat $minutesLate menit";
                     
                     $lateFeePerInterval = $this->company->late_fee_per_minute ?? 0;
                     $interval = $this->company->late_fee_interval_minutes ?? 1;
                     if ($interval > 0) {
                         $blocks = ceil($minutesLate / $interval);
                         $denda = $blocks * $lateFeePerInterval;
                     }
                 }
            }
        }

        return [
            $attendance->user->name ?? '-',
            $attendance->date_attendance,
            $attendance->time_in ?? '-',
            $attendance->time_out ?? '-',
            $statusLabel,
            $keterangan,
            'Rp ' . number_format($denda, 0, ',', '.')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1976D2']], // Biru (beda warna dari rekap)
            ],
            'A1:G' . $sheet->getHighestRow() => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            ],
        ];
    }
}