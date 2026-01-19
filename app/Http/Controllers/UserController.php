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

use Spatie\Permission\Models\Role;
use function Pest\Laravel\post;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.users.create', compact('roles'));
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
            'role' => 'required', // Role is required now
            'department' => 'nullable',
            'password' => 'required',
            'email' => 'required|email|unique:users,email',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->role, // Optionally store role name in position field for backward compatibility
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan' => $request->tunjangan,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('user.index')->with('success', 'Data karyawan berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'role' => 'required',
            'department' => 'nullable',
            'email' => 'required|email',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
        ]);
        
        $data = $request->except(['password', 'role']);
        
        // Update position explicitly if needed for backward compatibility
        $data['position'] = $request->role; 

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()->route('user.index')->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function delete($id) {
        $user = User::findOrFail($id);
        return view('pages.users.delete', compact('user'));
    }

    public function destroy(User $user) {
        $user->delete();

        return redirect()->route('user.index')->with('success', 'Karyawan berhasil dihapus');
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

        return Excel::download(new AttendanceExport((object)[
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => null
        ]), $filename);
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

    public function deleteAttendanceByMonth(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);
        
        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        // Delete attendance records for the specified month
        $deletedCount = Attendance::where('user_id', $user->id)
            ->whereBetween('date_attendance', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->delete();
        
        return redirect()->back()->with('success', "Deleted {$deletedCount} attendance records for {$month->format('F Y')}.");
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
