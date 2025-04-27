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

        $attendance = new Attendance;
        $attendance->user_id = $request->user()->id;
        $attendance->date_attendance = now()->toDateString();
        $attendance->time_in = now()->toTimeString();
        $attendance->latlon_in = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'checked in',
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
}