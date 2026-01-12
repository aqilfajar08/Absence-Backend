<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Company;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_attendance', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->orderBy('date_attendance', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $company = Company::first();
        return view('pages.attendances.index', compact('attendances', 'company')); 
    }

    public function export(Request $request) 
    {
        $filename = 'laporan-absensi-' . date('d-m-Y-H-i') . '.xlsx';
        return Excel::download(new AttendanceExport($request), $filename);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        
        return redirect()->route('attendance.index')->with('success', 'Attendance record deleted successfully.');
    }

    public function deleteByMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        // Parse the month string (YYYY-MM format)
        $monthParts = explode('-', $request->month);
        $year = (int) $monthParts[0];
        $month = (int) $monthParts[1];

        // Validate month range
        if ($month < 1 || $month > 12) {
            return redirect('/attendance')->with('error', 'Invalid month selected.');
        }

        // Create Carbon instances for start and end of month
        $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Delete attendance records for the specified month for ALL users
        $deletedCount = Attendance::whereRaw('DATE(date_attendance) BETWEEN ? AND ?', [
            $startOfMonth->format('Y-m-d'),
            $endOfMonth->format('Y-m-d')
        ])->delete();

                $monthName = $startOfMonth->format('F Y');

        if ($deletedCount > 0) {
            return redirect('/attendance')->with('success', "Berhasil menghapus {$deletedCount} data absensi untuk bulan {$monthName}.");
        } else {
            return redirect('/attendance')->with('error', "Tidak ada data absensi yang ditemukan untuk bulan {$monthName}.");
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate user role
        if (!auth()->user()->hasRole(['admin', 'receptionist'])) {
            return redirect()->route('attendance.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:ontime,late',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->is_late = ($request->status === 'late');
        $attendance->save();

        $statusText = $request->status === 'late' ? 'Terlambat' : 'Tepat Waktu';
        return redirect()->route('attendance.index')->with('success', "Status kehadiran berhasil diubah menjadi: {$statusText}");
    }
}