<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Approval\LeaveApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Executive\DashboardController as ExecutiveDashboardController;
use App\Http\Controllers\Finance\EmployeeCompensationController;
use App\Http\Controllers\Finance\PayrollController;
use App\Http\Controllers\Finance\ProjectIncentiveController;
use App\Http\Controllers\SanctionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// IoT Hardware Heartbeat & Sync Endpoint (Unauthenticated, CSRF excluded)
// IoT Hardware Heartbeat & RFID Tap Endpoints (Unauthenticated, CSRF excluded)
Route::post('/devices/heartbeat', [\App\Http\Controllers\Admin\DeviceController::class, 'heartbeat'])->name('devices.heartbeat');
Route::post('/devices/rfid-tap', [\App\Http\Controllers\Admin\DeviceController::class, 'rfidTap'])->name('devices.rfid-tap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // User Self-Service Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Super Admin Routes
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', fn() => redirect()->route('admin.dashboard'));
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/holidays', [SettingController::class, 'storeHoliday'])->name('settings.holidays.store');
        Route::delete('/settings/holidays/{holiday}', [SettingController::class, 'destroyHoliday'])->name('settings.holidays.destroy');

        // IoT Device & Hardware Maintenance
        Route::get('/devices/latest-scan', [\App\Http\Controllers\Admin\DeviceController::class, 'latestScan'])->name('devices.latest-scan');
        Route::resource('devices', \App\Http\Controllers\Admin\DeviceController::class)->except(['show', 'create', 'edit']);
        Route::patch('/devices/{device}/maintenance', [\App\Http\Controllers\Admin\DeviceController::class, 'toggleMaintenance'])->name('devices.maintenance');
        Route::patch('/devices/{device}/mode', [\App\Http\Controllers\Admin\DeviceController::class, 'setMode'])->name('devices.mode');

        // Attendance Logs
        // Attendance Logs & Manual Override
        Route::get('/attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
        Route::post('/attendances/manual-override', [\App\Http\Controllers\Admin\AttendanceController::class, 'manualOverride'])->name('attendances.override');
    });

    // Finance & HRD Routes (Finance & Super Admin)
    Route::middleware('role:finance,super_admin')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/compensations', [EmployeeCompensationController::class, 'index'])->name('compensations.index');
        Route::put('/compensations/{user}', [EmployeeCompensationController::class, 'update'])->name('compensations.update');

        Route::get('/payrolls', [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::post('/payrolls/publish', [PayrollController::class, 'publish'])->name('payroll.publish');

        Route::get('/incentives', [ProjectIncentiveController::class, 'index'])->name('incentives.index');
        Route::post('/incentives', [ProjectIncentiveController::class, 'store'])->name('incentives.store');
    });

    // Executive / CEO Routes
    Route::middleware('role:ceo')->prefix('executive')->name('executive.')->group(function () {
        Route::get('/dashboard', [ExecutiveDashboardController::class, 'index'])->name('dashboard');
    });

    // Leave Approvals Route (Super Admin or User with can_approve_leaves)
    Route::prefix('approvals')->name('approval.')->group(function () {
        Route::get('/', [LeaveApprovalController::class, 'index'])->name('index');
        Route::patch('/{leaveRequest}/approve', [LeaveApprovalController::class, 'approve'])->name('approve');
        Route::patch('/{leaveRequest}/reject', [LeaveApprovalController::class, 'reject'])->name('reject');
    });

    // Reports & Rekapitulasi Routes (Super Admin, CEO, Finance)
    Route::middleware('role:super_admin,ceo,finance')->group(function () {
        Route::get('/sanctions', [SanctionController::class, 'index'])->name('sanctions.index');
        Route::get('/sanctions/{employeeSanction}/pdf', [SanctionController::class, 'downloadPdf'])->name('sanctions.pdf');
    });

    Route::middleware('role:super_admin,finance')->prefix('sanctions')->name('sanctions.')->group(function () {
        Route::post('/detect', [SanctionController::class, 'detectAutoSanctions'])->name('detect');
        Route::post('/', [SanctionController::class, 'storeSanction'])->name('store');
        Route::put('/{employeeSanction}', [SanctionController::class, 'updateSanction'])->name('update');
        Route::post('/rules', [SanctionController::class, 'storeRule'])->name('rules.store');
        Route::put('/rules/{sanctionRule}', [SanctionController::class, 'updateRule'])->name('rules.update');
        Route::delete('/rules/{sanctionRule}', [SanctionController::class, 'destroyRule'])->name('rules.destroy');
    });

    Route::middleware('role:super_admin,ceo,finance')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/attendances', [\App\Http\Controllers\ReportController::class, 'attendances'])->name('attendances');
        Route::get('/attendances/export', [\App\Http\Controllers\ReportController::class, 'exportAttendances'])->name('attendances.export');
        Route::get('/payrolls', [\App\Http\Controllers\ReportController::class, 'payrolls'])->name('payrolls');
        Route::get('/payrolls/export', [\App\Http\Controllers\ReportController::class, 'exportPayrolls'])->name('payrolls.export');
        Route::get('/payrolls/{payroll}/pdf', [\App\Http\Controllers\ReportController::class, 'downloadSlipPdf'])->name('payrolls.pdf');
        Route::get('/leaves', [\App\Http\Controllers\ReportController::class, 'leaves'])->name('leaves');
        Route::get('/leaves/export', [\App\Http\Controllers\ReportController::class, 'exportLeaves'])->name('leaves.export');
        Route::get('/daily-tasks', [\App\Http\Controllers\ReportController::class, 'dailyTasks'])->name('daily-tasks');
        Route::get('/daily-tasks/export', [\App\Http\Controllers\ReportController::class, 'exportDailyTasks'])->name('daily-tasks.export');
        Route::post('/daily-tasks', [\App\Http\Controllers\ReportController::class, 'storeDailyTask'])->name('daily-tasks.store');
        Route::put('/daily-tasks/{dailyReport}', [\App\Http\Controllers\ReportController::class, 'updateDailyTask'])->name('daily-tasks.update');
    });

    // Location Attendance Check-in Route (GPS Web Portal)
    Route::post('/attendances/location-check-in', [\App\Http\Controllers\Admin\AttendanceController::class, 'locationCheckIn'])->name('attendances.location-check-in');

    // Interactive Mobile REST API & IoT Documentation Portal
    Route::get('/api-docs', fn() => view('api_docs.index'))->name('api.docs');
});

// API Endpoint for Location Attendance Check-in (Mobile / Kiosk / External GPS Sync)
Route::post('/api/attendances/location-check-in', function (\Illuminate\Http\Request $request, \App\Services\AttendanceService $attendanceService) {
    $validated = $request->validate([
        'user_id' => 'nullable|uuid|exists:users,id',
        'email' => 'nullable|email|exists:users,email',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'presence_type' => 'required|in:ON_SITE,WFA,AUTO',
        'notes' => 'nullable|string|max:255',
    ]);

    $user = null;
    if (!empty($validated['user_id'])) {
        $user = \App\Models\User::find($validated['user_id']);
    } elseif (!empty($validated['email'])) {
        $user = \App\Models\User::where('email', $validated['email'])->first();
    }

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Pengguna tidak ditemukan. Kirimkan parameter user_id atau email yang valid.',
        ], 404);
    }

    $result = $attendanceService->recordLocationAttendance(
        user: $user,
        latitude: (float) $validated['latitude'],
        longitude: (float) $validated['longitude'],
        presenceType: $validated['presence_type'],
        notes: $validated['notes'] ?? null
    );

    return response()->json([
        'success' => true,
        'action' => $result['action'],
        'message' => $result['message'],
        'distance' => $result['distance'],
        'is_within_radius' => $result['is_within_radius'],
        'attendance' => $result['attendance'],
    ]);
})->name('api.attendances.location-check-in');


