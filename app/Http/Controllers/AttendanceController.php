<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Company;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        // Check if filtering by a SINGLE day (e.g., 'Today' or custom same start/end)
        $isSingleDay = ($startDate && $endDate && $startDate === $endDate) || (!$startDate && !$endDate); 
        $targetDate = $startDate ?? Carbon::now('Asia/Makassar')->format('Y-m-d');
        
        $query = Attendance::with(['user', 'user.permits' => function($q) {
        $q->where('is_approved', 'approved');
    }]);

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

        // If viewing a single day, exclude users who are already present in the attendance list from the "Absent" list
        $absentUsers = collect([]);
        if ($isSingleDay) {
             // Get IDs of users who have attendance records for this date
             $presentUserIds = Attendance::where('date_attendance', $targetDate)->pluck('user_id');
             
             // Get users who are NOT in that list and NOT admins
             $absentQuery = \App\Models\User::whereNotIn('id', $presentUserIds)
                ->whereDoesntHave('roles', function($q) {
                    $q->where('name', 'admin');
                });
             
             if ($request->filled('search')) {
                 $absentQuery->where('name', 'like', '%' . $request->search . '%');
             }
             
             $absentUsers = $absentQuery->get();
        }

        return view('pages.attendances.index', compact('attendances', 'company', 'absentUsers', 'isSingleDay', 'targetDate')); 
    }

    // Store manual attendance (for absent users)
    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_attendance' => 'required|date',
            'status' => 'required|in:present,sick,permission,alpha,leave,out_of_town',
            'note' => 'nullable|string|max:255',
        ]);
        
        Attendance::create([
            'user_id' => $request->user_id,
            'date_attendance' => $request->date_attendance,
            'status' => $request->status,
            'note' => $request->note,
            'time_in' => ($request->status === 'present') ? Carbon::now('Asia/Makassar')->format('H:i:s') : null,
            'latlon_in' => null, // Manual entry
            'is_late' => false,
        ]);

        return back()->with('success', 'Status kehadiran berhasil dicatat.');
    }

    public function export(Request $request) 
    {
        $filename = 'laporan-absensi-' . Carbon::now('Asia/Makassar')->format('d-m-Y-H-i') . '.xlsx';
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

    public function scan()
    {
        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('date_attendance', Carbon::now('Asia/Makassar')->format('Y-m-d'))
                                ->first();
        return view('pages.attendances.scan', compact('attendance'));
    }

    public function createQr()
    {
        // Token hari ini (bisa diperkuat dengan salt/hash jika perlu)
        $qrToken = 'KASAU-ABSENSI-' . Carbon::now('Asia/Makassar')->format('Y-m-d');
        
        // Data kehadiran hari ini untuk monitoring resepsionis
        // Filter: Hanya yang sudah absen (Hadir/Terlambat), abaikan Izin/Cuti/dll (yang time_in null)
        $todayAttendances = Attendance::with('user')
            ->where('date_attendance', Carbon::now('Asia/Makassar')->format('Y-m-d'))
            ->whereNotNull('time_in')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $company = Company::first();

        return view('pages.attendances.create-qr', compact('qrToken', 'todayAttendances', 'company'));
    }

    // Memproses hasil scan QR Code
    public function processScan(Request $request) 
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'qr_code' => 'required|string', 
        ]);

        // Validasi Token QR
        // Jika QR Code adalah "CHECKOUT_BUTTON", lewati validasi token (hanya untuk checkout)
        if ($request->qr_code !== 'CHECKOUT_BUTTON') {
            $validToken = 'KASAU-ABSENSI-' . Carbon::now('Asia/Makassar')->format('Y-m-d');
            if ($request->qr_code !== $validToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code tidak valid atau sudah kadaluarsa.'
                ], 422);
            }
        }



        /** @var \App\Models\User $user */
        $user = Auth::user();
        $carbonNow = Carbon::now('Asia/Makassar');
        $today = $carbonNow->format('Y-m-d');
        $now = $carbonNow->format('H:i');
        
        $company = Company::first();
        
        // 1. Validasi Lokasi (Geofencing)
        if ($company && $company->latitude && $company->longitude) {
            $distance = $this->calculateDistance($request->latitude, $request->longitude, $company->latitude, $company->longitude);
            $radiusKm = $company->radius_km ?? 1.0; // Default 1km
            
            if ($distance > $radiusKm) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Anda berada di luar radius kantor. Jarak: " . number_format($distance, 2) . " km. (Max: {$radiusKm} km)"
                ], 422);
            }
        }

        // 2. Cek Absensi Hari Ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date_attendance', $today)
                                ->first();

        // BLOCK: Check validation for manual status (Izin/Sakit/Cuti/etc)
        if ($attendance && in_array($attendance->status, ['sick', 'permission', 'leave', 'alpha', 'out_of_town'])) {
            $statusMap = [
                'sick' => 'Sakit', 
                'permission' => 'Izin', 
                'leave' => 'Cuti', 
                'alpha' => 'Alpha', 
                'out_of_town' => 'Dinas Luar Kota'
            ];
            $label = $statusMap[$attendance->status] ?? $attendance->status;
            return response()->json([
                'status' => 'error',
                'message' => "Anda tercatat dengan status {$label} hari ini. Tidak dapat melakukan absen."
            ], 422);
        }

        // 3. Logika Absen Masuk atau Pulang
        if (!$attendance) {
            // == ABSEN MASUK ==
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date_attendance' => $today,
                'time_in' => $now,
                'latlon_in' => "{$request->latitude},{$request->longitude}",
                'is_late' => ($now > ($company->time_in ?? '08:00')), // Cek telat sederhana
            ]);
            
            $status = $attendance->is_late ? 'Terlambat' : 'Tepat Waktu';
            return response()->json([
                'status' => 'success',
                'type' => 'in',
                'message' => "Absen Masuk Berhasil! Status: {$status} ({$now})",
                'data' => $attendance
            ]);

        } else {
            // == ABSEN PULANG ==
            if ($attendance->time_out) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah melakukan absen pulang hari ini.'
                ], 422);
            }

            $attendance->update([
                'time_out' => $now,
                'latlon_out' => "{$request->latitude},{$request->longitude}",
            ]);

            return response()->json([
                'status' => 'success',
                'type' => 'out',
                'message' => "Absen Pulang Berhasil! ({$now})",
                'data' => $attendance
            ]);
        }
    }

    // Absensi Khusus Resepsionis (Tanpa QR, Cek Lokasi)
    public function receptionistAttendance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out',
        ]);



        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Pastikan hanya resepsionis
        if (!$user->hasRole('resepsionis')) {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        $company = Company::first();
        
        // 1. Validasi Lokasi (Geofencing)
        if ($company && $company->latitude && $company->longitude) {
            $distance = $this->calculateDistance($request->latitude, $request->longitude, $company->latitude, $company->longitude);
            $radiusKm = $company->radius_km ?? 1.0; // Default 1km
            
            if ($distance > $radiusKm) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Anda berada di luar radius kantor ($distance km). Silakan ke kantor untuk absen."
                ], 422);
            }
        }

        $carbonNow = Carbon::now('Asia/Makassar');
        $today = $carbonNow->format('Y-m-d');
        $now = $carbonNow->format('H:i:s');

        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date_attendance', $today)
                                ->first();

        // BLOCK: Check validation for manual status
        if ($attendance && in_array($attendance->status, ['sick', 'permission', 'leave', 'alpha', 'out_of_town'])) {
             $statusMap = [
                'sick' => 'Sakit', 
                'permission' => 'Izin', 
                'leave' => 'Cuti', 
                'alpha' => 'Alpha', 
                'out_of_town' => 'Dinas Luar Kota'
            ];
            $label = $statusMap[$attendance->status] ?? $attendance->status;
            return response()->json(['status' => 'error', 'message' => "Anda tercatat dengan status {$label}. Tidak bisa absen."], 422);
        }

        if ($request->type === 'in') {
            if ($attendance) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen masuk hari ini.'], 422);
            }

            Attendance::create([
                'user_id' => $user->id,
                'date_attendance' => $today,
                'time_in' => $now,
                'latlon_in' => "{$request->latitude},{$request->longitude}",
                'is_late' => ($now > ($company->time_in ?? '08:00:00')),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Absen Masuk Berhasil!']);
        } 
        
        if ($request->type === 'out') {
            if (!$attendance) {
                return response()->json(['status' => 'error', 'message' => 'Anda belum absen masuk.'], 422);
            }
            if ($attendance->time_out) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen pulang hari ini.'], 422);
            }

            $attendance->update([
                'time_out' => $now,
                'latlon_out' => "{$request->latitude},{$request->longitude}",
            ]);

            return response()->json(['status' => 'success', 'message' => 'Absen Pulang Berhasil!']);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:ontime,late,sick,permission,alpha,leave,out_of_town', // Extended status
            'note' => 'nullable|string|max:500',
        ]);

        // Map UI status to DB status/is_late logic
        $dbStatus = 'present';
        $isLate = false;

        if ($request->status === 'late') {
            $dbStatus = 'present';
            $isLate = true;
        } elseif ($request->status === 'ontime') {
            $dbStatus = 'present';
            $isLate = false;
        } else {
            $dbStatus = $request->status; // sick, permission, alpha, leave, out_of_town
            $isLate = false;
        }

        $attendance->update([
            'status' => $dbStatus,
            'is_late' => $isLate,
            'note' => $request->note
        ]);

        return back()->with('success', 'Status dan keterangan berhasil diperbarui.');
    }

    // Update Catatan Absensi (Resepsionis)
    public function updateNote(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $request->validate([
            'note' => 'required|string|max:255',
        ]);

        $attendance->update([
            'note' => $request->note
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }
    
    // Menampilkan halaman Riwayat Absensi (Kalender)
    public function history()
    {
        // Ambil data absensi user yang login (misal 1 tahun terakhir)
        $attendances = Attendance::where('user_id', Auth::id())
                                ->whereYear('date_attendance', Carbon::now('Asia/Makassar')->format('Y'))
                                ->get()
                                ->mapWithKeys(function ($item) {
                                    return [\Carbon\Carbon::parse($item->date_attendance)->format('Y-m-d') => $item];
                                });
                                
        return view('pages.attendances.history', compact('attendances'));
    }

    // Helper calculate distance
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius bumi dalam km
      
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
      
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
      
        return $earthRadius * $c;
    }
}