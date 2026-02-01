<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    /**
     * Get current server time in Asia/Makassar timezone
     * This ensures all clients use consistent server time regardless of device timezone
     */
    public function getCurrentTime(Request $request)
    {
        $now = Carbon::now('Asia/Makassar');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'datetime' => $now->toDateTimeString(), // YYYY-MM-DD HH:MM:SS
                'date' => $now->toDateString(), // YYYY-MM-DD
                'time' => $now->toTimeString(), // HH:MM:SS
                'timestamp' => $now->timestamp,
                'timezone' => 'Asia/Makassar',
                'day_name_id' => $now->locale('id')->dayName, // Minggu, Senin, dst
                'day_name_en' => $now->dayName,
                'formatted_id' => $now->locale('id')->isoFormat('dddd, D MMMM YYYY'), // Minggu, 4 Februari 2026
                'formatted_en' => $now->format('l, d F Y'),
            ]
        ], 200);
    }
}
