<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('home') : view('pages.auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1') // 5 attempts per 1 minute
    ->name('login.attempt');

Route::get('/fix-lateness', function () {
    $company = \App\Models\Company::first();
    $limit = $company->time_in ?? '08:00';
    $limitHtml = substr($limit, 0, 5);
    
    $attendances = \App\Models\Attendance::whereNotNull('time_in')->get();
    $count = 0;
    foreach ($attendances as $att) {
         $timeInHtml = \Carbon\Carbon::parse($att->time_in)->format('H:i');
         $shouldBeLate = $timeInHtml > $limitHtml;
         
         if ($att->is_late != $shouldBeLate) {
             $att->is_late = $shouldBeLate;
             $att->save();
             $count++;
         }
    }
    return "Fixed $count records using limit $limitHtml";
});


Route::middleware(['auth', 'prevent.back.history'])->group(function () {
    // Dashboard - harus di dalam auth middleware!
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('user', UserController::class);
    Route::get('user/export/attendance', [UserController::class, 'exportAttendance'])->name('user.export.attendance');
    Route::get('user/{id}/attendance', [UserController::class, 'userAttendance'])->name('user.attendance');
    Route::delete('user/{id}/attendance', [UserController::class, 'deleteAttendanceByMonth'])->name('user.attendance.delete');
    // Route::get('user/{id}/permission', [UserController::class, 'userPermission'])->name('user.permission');
    
    Route::resource('company', CompanyController::class)->except('create', 'index', 'destroy');
    
    Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    
    // Scan QR Routes
    Route::get('attendance/create-qr', [AttendanceController::class, 'createQr'])->name('attendance.create-qr');
    Route::get('attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('attendance/scan', [AttendanceController::class, 'processScan'])->name('attendance.process_scan');
    Route::post('attendance/receptionist', [AttendanceController::class, 'receptionistAttendance'])->name('attendance.receptionist');
    Route::post('attendance/{id}/update-note', [AttendanceController::class, 'updateNote'])->name('attendance.update-note');
    
    // History Route
    Route::get('attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    Route::resource('attendance', AttendanceController::class);
    Route::post('attendance/delete-by-month', [AttendanceController::class, 'deleteByMonth'])->name('attendance.deleteByMonth');
    Route::post('attendance/store-manual', [AttendanceController::class, 'storeManual'])->name('attendance.storeManual');
    Route::put('attendance/{id}/update-status', [AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');

    // Profile routes
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Permission module skipped
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
    // Route::resource('permission', PermissionController::class);
});

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    return response()->file($filePath);
})->where('path', '.*');