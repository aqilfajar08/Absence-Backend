<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class MonthlyAttendanceSheet implements FromView, ShouldAutoSize, WithTitle
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
        return 'Rekap ' . Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('M Y');
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
        $daysInMonth = Carbon::createFromDate($this->year, $this->month, 1)->daysInMonth;

        return view('exports.monthly_attendance', [
            'users' => $users,
            'year' => $this->year,
            'month' => $this->month,
            'daysInMonth' => $daysInMonth,
            'company' => $this->company
        ]);
    }
}