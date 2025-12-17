<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')->orderBy('id', 'desc')->paginate(10); 
        return view('pages.attendances.index', compact('attendances')); 
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
        return redirect('/attendance')->with('success', "Successfully deleted {$deletedCount} attendance records for {$monthName} across all users.");
    }
}