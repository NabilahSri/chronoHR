<?php

use App\Http\Controllers\Api\MobileApiController;
use Illuminate\Support\Facades\Route;

// 1. Endpoint Publik: Branding Aplikasi, Login Mobile & IoT Hardware Tap
Route::get('/branding', [MobileApiController::class, 'branding'])->name('api.mobile.branding');
Route::post('/login', [MobileApiController::class, 'login'])->name('api.mobile.login');
Route::post('/devices/rfid-tap', [\App\Http\Controllers\Admin\DeviceController::class, 'rfidTap'])->name('api.devices.rfid-tap');

// Endpoint Terproteksi Token (Bearer Token / Sanctum Compatible)
Route::middleware('auth.api')->name('api.mobile.')->group(function () {

    // 1. Autentikasi & Profil (Auth & Profile)
    Route::post('/logout', [MobileApiController::class, 'logout'])->name('logout');
    Route::get('/profile', [MobileApiController::class, 'profile'])->name('profile');
    Route::put('/profile/password', [MobileApiController::class, 'updatePassword'])->name('profile.password');

    // 2. Presensi & Riwayat Kehadiran (Attendances)
    Route::get('/attendance/today', [MobileApiController::class, 'attendanceToday'])->name('attendance.today');
    Route::post('/attendance/wfh', [MobileApiController::class, 'attendanceWfaCheckIn'])->name('attendance.wfh');
    Route::post('/attendance/wfa', [MobileApiController::class, 'attendanceWfaCheckIn'])->name('attendance.wfa');
    Route::post('/attendance/checkout', [MobileApiController::class, 'attendanceCheckout'])->name('attendance.checkout');
    Route::get('/attendance/history', [MobileApiController::class, 'attendanceHistory'])->name('attendance.history');

    // 3. Daily Report - Tugas Pagi & Sore (Daily Reports)
    Route::get('/reports/today', [MobileApiController::class, 'reportToday'])->name('reports.today');
    Route::post('/reports/morning', [MobileApiController::class, 'reportMorning'])->name('reports.morning');
    Route::post('/reports/evening', [MobileApiController::class, 'reportEvening'])->name('reports.evening');
    Route::get('/reports/history', [MobileApiController::class, 'reportHistory'])->name('reports.history');

    // 4. Manajemen Perizinan & Dynamic Approval (Leave Requests)
    Route::get('/leaves', [MobileApiController::class, 'leavesIndex'])->name('leaves.index');
    Route::post('/leaves', [MobileApiController::class, 'leavesStore'])->name('leaves.store');
    Route::get('/leaves/approvals', [MobileApiController::class, 'leavesApprovals'])->name('leaves.approvals');
    Route::post('/leaves/{leaveRequest}/action', [MobileApiController::class, 'leavesAction'])->name('leaves.action');

    // 5. Finansial: Slip Gaji & Insentif Proyek (Payrolls & Incentives)
    Route::get('/payrolls', [MobileApiController::class, 'payrollsIndex'])->name('payrolls.index');
    Route::get('/payrolls/{payroll}', [MobileApiController::class, 'payrollsShow'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/download', [MobileApiController::class, 'payrollsDownload'])->name('payrolls.download');
    Route::get('/incentives', [MobileApiController::class, 'incentivesIndex'])->name('incentives.index');

    // 6. Sanksi & Evaluasi Kedisiplinan (Sanctions)
    Route::get('/sanctions', [MobileApiController::class, 'sanctionsIndex'])->name('sanctions.index');
    Route::get('/sanctions/{employeeSanction}', [MobileApiController::class, 'sanctionsShow'])->name('sanctions.show');
    Route::get('/sanctions/{employeeSanction}/download', [MobileApiController::class, 'sanctionsDownload'])->name('sanctions.download');
});
