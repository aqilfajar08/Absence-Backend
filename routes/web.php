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

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('user', UserController::class);
Route::resource('company', CompanyController::class)->except('create', 'index', 'destroy');
Route::resource('attendance', AttendanceController::class);
Route::resource('permission', PermissionController::class);