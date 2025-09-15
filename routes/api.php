<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
    

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Face Detection Routes
Route::post('/user/register-face', [AuthController::class, 'registerFace'])->middleware('auth:sanctum');

// Company & Attendance Routes
Route::get('/show-company', [CompanyController::class, 'show'])->middleware('auth:sanctum');
Route::post('/attendances/checkin', [AttendanceController::class, 'checkin'])->middleware('auth:sanctum');
Route::post('/attendances/checkout', [AttendanceController::class, 'checkout'])->middleware('auth:sanctum');
Route::get('/attendances/status', [AttendanceController::class, 'checkStatus'])->middleware('auth:sanctum');

// Permission Routes
Route::get('/permission/test', [PermissionController::class, 'test'])->middleware('auth:sanctum');
Route::post('/permission', [PermissionController::class, 'store'])->middleware('auth:sanctum');

// update fcm token
Route::post('/user/update-fcm-token', [AuthController::class, 'updateFcmToken'])->middleware('auth:sanctum');

// Profile image routes
Route::post('/user/upload-profile-image', [AuthController::class, 'uploadProfileImage'])->middleware('auth:sanctum');
Route::get('/user/profile', [AuthController::class, 'getProfile'])->middleware('auth:sanctum');
Route::delete('/user/delete-profile-image', [AuthController::class, 'deleteProfileImage'])->middleware('auth:sanctum');

// get attendance
Route::get('/api-attendances', [AttendanceController::class, 'index'])->middleware('auth:sanctum');