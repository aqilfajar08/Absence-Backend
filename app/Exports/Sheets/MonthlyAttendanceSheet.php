<?php

namespace App\Exports\Sheets;

use App\Models\User;
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

class MonthlyAttendanceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
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

    // Judul Sheet (Contoh: Januari 2026)
    public function title(): string
    {
        return 'Rekap ' . Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('M Y');
    }

    public function collection()
    {
        // Ambil User karyawan
        $query = User::query();

        // Jika ada filter search nama
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Dan load absensi MEREKA HANYA pada bulan & tahun sheet ini
        $query->with(['attendance' => function($q) {
            $q->whereYear('date_attendance', $this->year)
              ->whereMonth('date_attendance', $this->month);
        }]);

        // Filter user yang setidaknya punya absensi di bulan ini ATAU user aktif?
        // Untuk laporan gaji, sebaiknya tampilkan semua user aktif meski absen kosong
        // Tapi agar rapi, kita ambil semua user.
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Jabatan',
            'Jumlah Kehadiran',
            'Jumlah Terlambat',
            'Gaji Pokok',
            'Tunjangan',
            'Total Denda (Akumulasi)',
            'Gaji Bersih (GP + TJ - Denda)'
        ];
    }

    public function map($user): array
    {
        $totalDenda = 0;
        $lateCount = 0;
        $attendances = $user->attendance; // Absensi bulan ini saja (karena sudah di-filter di collection)
        $presenceCount = $attendances->count();

        // Hitung Denda dari setiap hari keterlambatan
        foreach ($attendances as $attendance) {
            if ($attendance->is_late && $attendance->time_in) {
                $timeIn = Carbon::parse($attendance->time_in);
                $cutoffTime = Carbon::parse($this->company->time_in ?? '09:00:00');

                if ($timeIn->gt($cutoffTime)) {
                    $lateCount++; // Tambah counter telat
                    
                    // Hitung nominal denda harian
                    $minutesLate = abs($timeIn->diffInMinutes($cutoffTime));
                    $lateFeePerInterval = $this->company->late_fee_per_minute ?? 1000;
                    $interval = $this->company->late_fee_interval_minutes ?? 1;
                    $blocks = ceil($minutesLate / $interval);
                    
                    $dailyFine = $blocks * $lateFeePerInterval;
                    $totalDenda += $dailyFine; // Akumulasi ke total sebulan
                }
            }
        }

        $gajiPokok = $user->gaji_pokok ?? 0;
        $tunjangan = $user->tunjangan ?? 0;
        
        // Kalkulasi Akhir
        $gajiBersih = max(0, ($gajiPokok + $tunjangan) - $totalDenda);

        return [
            $user->name,
            ucfirst($user->getRoleNames()->first() ?? '-'),
            $presenceCount . ' Hari',
            $lateCount . ' Kali',
            'Rp ' . number_format($gajiPokok, 0, ',', '.'),
            'Rp ' . number_format($tunjangan, 0, ',', '.'),
            'Rp ' . number_format($totalDenda, 0, ',', '.'), // Ini total denda sebulan (cth: 65.000)
            'Rp ' . number_format($gajiBersih, 0, ',', '.')  // Ini hasil akhir
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF800000']],
            ],
            'A1:H' . $sheet->getHighestRow() => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
            ],
            // Auto width akan ditangani oleh ShouldAutoSize
        ];
    }
}