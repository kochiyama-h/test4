<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/admin/login', function () {
    return view('auth.admin_login');
})->name('admin.login');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout');





Route::prefix('attendance')->group(function () {
    Route::get('', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('finish', [AttendanceController::class, 'finish'])->name('attendance.finish');
    Route::post('break/start', [AttendanceController::class, 'breakStart'])->name('attendance.break.start');
    Route::post('break/end', [AttendanceController::class, 'breakEnd'])->name('attendance.break.end');
    Route::get('list', [AttendanceController::class, 'list'])->name('attendance.list');
});



Route::middleware('auth')->group(function () {
    Route::get('admin/stamp_correction_request/list', [RequestController::class, 'adminRequest'])->name('admin.request');
});

Route::get('stamp_correction_request/list', [RequestController::class, 'request'])->name('request');





Route::post('stamp_correction_request/approve/{attendance_correct_request}', [RequestController::class, 'adminApproveDetail'])->name('admin.approve.detail');

Route::get('stamp_correction_request/list', [RequestController::class, 'request'])->name('request');
Route::get('stamp_correction_request/approve', [RequestController::class, 'adminApprove'])->name('admin.approve');



Route::get('attendance/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
Route::put('attendance/{attendance}', [RequestController::class, 'update'])->name('attendance.update');

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'adminLogin']);
    Route::get('attendance/list', [AttendanceController::class, 'adminList'])->name('admin.attendance.list');
    Route::get('staff/list', [AttendanceController::class, 'adminStaffList'])->name('admin.staff.list');
    Route::get('attendance/staff/{id}', [AttendanceController::class, 'adminStaffAttendance'])->name('admin.staff.attendance');
    Route::get('attendance/{id}', [AttendanceController::class, 'adminDetail'])->name('admin.attendance.detail');
    Route::put('attendance/{attendance}', [RequestController::class, 'recordChange'])->name('admin.record.change');
    

    
});



