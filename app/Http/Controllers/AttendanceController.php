<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        try {
            // Log the request for debugging
            Log::info('Delete by month request', [
                'month' => $request->month,
                'all_data' => $request->all()
            ]);

            $request->validate([
                'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            ]);

            // Parse the month string (YYYY-MM format)
            $monthParts = explode('-', $request->month);
            $year = (int) $monthParts[0];
            $month = (int) $monthParts[1];

            // Validate month range
            if ($month < 1 || $month > 12) {
                Log::warning('Invalid month selected', ['month' => $month]);
                return redirect('/attendance')->with('error', 'Invalid month selected.');
            }

            // Create Carbon instances for start and end of month
            $startOfMonth = Carbon::create($year, $month, 1, 0, 0, 0);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            Log::info('Date range calculated', [
                'start' => $startOfMonth->format('Y-m-d'),
                'end' => $endOfMonth->format('Y-m-d')
            ]);

            // Delete attendance records for the specified month for ALL users
            // Use DATE() function to compare only the date part
            $deletedCount = Attendance::whereRaw('DATE(date_attendance) BETWEEN ? AND ?', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])->delete();

            Log::info('Deletion completed', ['deleted_count' => $deletedCount]);

            $monthName = $startOfMonth->format('F Y');
            return redirect('/attendance')->with('success', "Successfully deleted {$deletedCount} attendance records for {$monthName} across all users.");

        } catch (\Exception $e) {
            Log::error('Error in deleteByMonth', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/attendance')->with('error', 'An error occurred while deleting attendance data. Please try again.');
        }
    }
}