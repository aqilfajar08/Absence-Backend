<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Permit;
use App\Exports\AttendanceExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use function Pest\Laravel\post;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        return view('pages.users.create');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Get current month start and end dates
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Get attendance count for current month
        $attendanceCount = Attendance::where('user_id', $user->id)
            ->whereBetween('date_attendance', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->count();
            
        // Get permission count for current month
        $permissionCount = Permit::where('user_id', $user->id)
            ->whereBetween('date_permission', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->count();
            
        return view('pages.users.show', compact('user', 'attendanceCount', 'permissionCount', 'currentMonth'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'position' => 'nullable',
            'department' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan' => $request->tunjangan,
        ]);
        return redirect()->route('user.index')->with('success', 'User created successfully');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'position' => 'nullable',
            'department' => 'required',
            'email' => 'required|email',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
        ]);
        
        $data = $request->except('password');

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    public function delete($id) {
        $user = User::findOrFail($id);
        return view('pages.users.delete', compact('user'));
    }

    public function destroy(User $user) {
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }

    public function exportAttendance(Request $request)
    {
        $request->validate([
            'selected_month' => 'required|date_format:Y-m',
        ]);

        // Parse the selected month (format: Y-m, e.g., "2025-01")
        $selectedMonth = $request->input('selected_month');
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        
        // Get start and end dates for the entire month
        $startDate = $monthDate->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        
        // Generate filename with month name
        $filename = 'attendance_report_' . 
                   $monthDate->format('F_Y') . '.xlsx';

        return Excel::download(new AttendanceExport($startDate, $endDate), $filename);
    }

    public function userAttendance($id)
    {
        $user = User::findOrFail($id);
        
        // Get current month start and end dates
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Get attendance records for current month
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date_attendance', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date_attendance', 'desc')
            ->paginate(15);
            
        return view('pages.users.attendance', compact('user', 'attendances', 'currentMonth'));
    }

    public function userPermission($id)
    {
        $user = User::findOrFail($id);
        
        // Get current month start and end dates
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Get permission records for current month
        $permissions = Permit::where('user_id', $user->id)
            ->whereBetween('date_permission', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date_permission', 'desc')
            ->paginate(15);
            
        return view('pages.users.permission', compact('user', 'permissions', 'currentMonth'));
    }
}
