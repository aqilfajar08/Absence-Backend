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
Route::get('/show-company', [CompanyController::class, 'show'])->middleware('auth:sanctum');
Route::post('/attendances/checkin', [AttendanceController::class, 'checkin'])->middleware('auth:sanctum');
Route::post('/attendances/checkout', [AttendanceController::class, 'checkout'])->middleware('auth:sanctum');
Route::post('/permission', [PermissionController::class, 'store'])->middleware('auth:sanctum');