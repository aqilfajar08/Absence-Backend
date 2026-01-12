<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.auth.login');
});

// Temporary route to generate dummy data
// Route::get('/generate-dummy-attendance', function () {
//     \App\Models\Attendance::factory(50)->create();
//     return redirect('/attendance')->with('success', '50 data absensi dummy berhasil dibuat!');
// });

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('user', UserController::class);
    Route::get('user/export/attendance', [UserController::class, 'exportAttendance'])->name('user.export.attendance');
    Route::get('user/{id}/attendance', [UserController::class, 'userAttendance'])->name('user.attendance');
    Route::delete('user/{id}/attendance', [UserController::class, 'deleteAttendanceByMonth'])->name('user.attendance.delete');
    // Route::get('user/{id}/permission', [UserController::class, 'userPermission'])->name('user.permission');
    
    Route::resource('company', CompanyController::class)->except('create', 'index', 'destroy');
    
    Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::resource('attendance', AttendanceController::class);
    Route::post('attendance/delete-by-month', [AttendanceController::class, 'deleteByMonth'])->name('attendance.deleteByMonth');
    Route::put('attendance/{id}/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');
    
    // Permission module skipped
    // Route::resource('permission', PermissionController::class);
});

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    return response()->file($filePath);
})->where('path', '.*');