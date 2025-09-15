<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkin(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $user = $request->user();

        // Check if already checked in today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date_attendance', now()->toDateString())
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
        $attendance->date_attendance = now()->toDateString();
        $attendance->time_in = now()->toTimeString();
        $attendance->latlon_in = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'checked in successfully',
            'attendance' => $attendance,
        ], 201);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        // GET User for checkout
        $attendance = Attendance::where('user_id', $request->user()->id)->where('date_attendance', now()->toDateString())->first();
        $attendance->time_out = now()->toTimeString();
        $attendance->latlon_out = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'checked out',
            'attendance' => $attendance,
        ], 200);
    }

    public function checkStatus(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

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
            'attendance' => $attendance,
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

        return response()->json([
            'status' => 'success',
            'data' => $attendance,
        ], 200);
    }
}