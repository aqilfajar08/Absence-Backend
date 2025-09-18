<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkin(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $user = $request->user();

        // Get current time in WITA timezone
        $witaTime = Carbon::now('Asia/Makassar');
        
        // Check if already checked in today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date_attendance', $witaTime->toDateString())
            ->whereNull('time_out')
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'Already checked in today'
            ], 400);
        }

        $attendance = new Attendance;
        $attendance->user_id = $user->id;
        $attendance->date_attendance = $witaTime->toDateString();
        $attendance->time_in = $witaTime->toTimeString();
        $attendance->latlon_in = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'checked in successfully',
            'attendance' => [
                'id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date_attendance' => $attendance->date_attendance_formatted,
                'time_in' => $attendance->time_in_formatted,
                'time_out' => $attendance->time_out_formatted,
                'latlon_in' => $attendance->latlon_in,
                'latlon_out' => $attendance->latlon_out,
                'wita_timezone' => 'Asia/Makassar',
            ],
        ], 201);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        // Get current time in WITA timezone
        $witaTime = Carbon::now('Asia/Makassar');
        
        // GET User for checkout
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('date_attendance', $witaTime->toDateString())
            ->first();
            
        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'No check-in record found for today'
            ], 404);
        }
        
        $attendance->time_out = $witaTime->toTimeString();
        $attendance->latlon_out = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'checked out',
            'attendance' => [
                'id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date_attendance' => $attendance->date_attendance_formatted,
                'time_in' => $attendance->time_in_formatted,
                'time_out' => $attendance->time_out_formatted,
                'latlon_in' => $attendance->latlon_in,
                'latlon_out' => $attendance->latlon_out,
                'wita_timezone' => 'Asia/Makassar',
            ],
        ], 200);
    }

    public function checkStatus(Request $request)
    {
        $user = $request->user();
        $witaTime = Carbon::now('Asia/Makassar');
        $today = $witaTime->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date_attendance', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'success',
                'is_checked_in' => false,
                'message' => 'Not checked in today'
            ]);
        }

        $isCheckedIn = $attendance->time_in && !$attendance->time_out;

        return response()->json([
            'status' => 'success',
            'is_checked_in' => $isCheckedIn,
            'attendance' => [
                'id' => $attendance->id,
                'user_id' => $attendance->user_id,
                'date_attendance' => $attendance->date_attendance_formatted,
                'time_in' => $attendance->time_in_formatted,
                'time_out' => $attendance->time_out_formatted,
                'latlon_in' => $attendance->latlon_in,
                'latlon_out' => $attendance->latlon_out,
                'wita_timezone' => 'Asia/Makassar',
            ],
            'message' => $isCheckedIn ? 'Currently checked in' : 'Already checked out'
        ]);
    }

    // index attendance
    public function index(Request $request) {
        $date = $request->input('date');

        $currentUser = $request->user();

        $query = Attendance::where('user_id', $currentUser->id);

        if ($date) {
            $query->where('date_attendance', $date);
        }
        
        $attendance = $query->get();

        // Format attendance data with WITA timezone
        $formattedAttendance = $attendance->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'date_attendance' => $item->date_attendance_formatted,
                'time_in' => $item->time_in_formatted,
                'time_out' => $item->time_out_formatted,
                'latlon_in' => $item->latlon_in,
                'latlon_out' => $item->latlon_out,
                'wita_timezone' => 'Asia/Makassar',
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedAttendance,
            'timezone' => 'Asia/Makassar (WITA)',
        ], 200);
    }
}