<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $today = date('Y-m-d');
        $company = Company::first(); // Data perusahaan

        $totalEmployees = User::count();
        $attendancesToday = Attendance::where('date_attendance', $today)->with('user')->get();

        $totalPresent = $attendancesToday->filter(function ($att) {
            return !in_array($att->status, ['leave', 'sick', 'permission', 'alpha']);
        })->count();

        $totalLate = $attendancesToday->where('is_late', true)->count();
        $totalAbsent = max(0, $totalEmployees - $totalPresent);

        $latestActivities = Attendance::where('date_attendance', $today)
                                      ->with('user')
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        // Data absensi user login hari ini
        $attendanceToday = null;
        $historyAttendances = [];
        
        if(Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            
            // Absensi Hari Ini
            $attendanceToday = Attendance::where('user_id', $user->id)
                                         ->where('date_attendance', $today)
                                         ->first();
            
            // History Absensi (Untuk Kalender)
            $historyAttendances = Attendance::where('user_id', $user->id)
                                    ->whereYear('date_attendance', date('Y'))
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [\Carbon\Carbon::parse($item->date_attendance)->format('Y-m-d') => $item];
                                    });
        }

        return response()
            ->view('pages.dashboard', compact(
                'totalEmployees',
                'totalPresent',
                'totalLate',
                'totalAbsent',
                'latestActivities',
                'company',
                'attendanceToday',
                'historyAttendances'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}