<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $today = date('Y-m-d');
        $company = Company::first(); // Data perusahaan

        $totalEmployees = User::count();
        $attendancesToday = Attendance::where('date_attendance', $today)->with('user')->get();

        $totalPresent = $attendancesToday->count();
        $totalLate = $attendancesToday->where('is_late', true)->count();
        $totalAbsent = max(0, $totalEmployees - $totalPresent);

        $latestActivities = Attendance::where('date_attendance', $today)
                                      ->with('user')
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();

        return view('pages.dashboard', compact(
            'totalEmployees',
            'totalPresent',
            'totalLate',
            'totalAbsent',
            'latestActivities',
            'company'
        ));
    }
}