<?php

namespace App\Http\Controllers\Api;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyReport;
use App\Models\EmployeeSanction;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\ProjectIncentive;
use App\Models\SanctionRule;
use App\Models\Setting;
use App\Models\User;
use App\Services\AttendanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class MobileApiController extends Controller
{
    public function __construct(protected AttendanceService $attendanceService)
    {
    }

    /**
     * Format data user standar untuk konsumsi aplikasi mobile Flutter
     */
    private function formatUserPayload(User $user): array
    {
        $user->loadMissing('roles');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'rfid_uid' => $user->rfid_uid,
            'employment_type' => $user->employment_type,
            'basic_salary' => (float) ($user->basic_salary ?? 0),
            'can_approve_leaves' => (bool) $user->can_approve_leaves,
            'is_attendance_required' => $user->isAttendanceRequired(),
            'roles' => $user->roles->pluck('name')->values()->all(),
            'role_details' => $user->roles->map(fn ($r) => [
                'name' => $r->name,
                'display_name' => $r->display_name,
            ])->values()->all(),
            'bank_account' => [
                'bank_name' => $user->bank_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_account_holder' => $user->bank_account_holder ?? $user->name,
            ],
            'ui_flags' => [
                'show_monthly_payslip_menu' => $user->employment_type !== 'INTERN',
                'show_project_incentive_menu' => true,
                'primary_attendance_mode' => $user->employment_type === 'INTERN' ? 'FACE_LOCATION' : 'RFID_OR_WFA',
                'show_leave_approval_menu' => (bool) $user->can_approve_leaves || $user->hasRole('super_admin'),
            ],
        ];
    }

    // =========================================================================
    // 0. BRANDING APLIKASI (PUBLIC)
    // =========================================================================

    private function formatBrandPayload(Request $request): array
    {
        Setting::clearBrandCache();
        $brand = Setting::getBrandSettings();
        $logoPath = $brand['logo_path'];

        return [
            'app_name' => $brand['name'],
            'app_slogan' => $brand['slogan'],
            'app_logo_path' => $logoPath,
            'app_logo_url' => $logoPath
                ? rtrim($request->getSchemeAndHttpHost(), '/') . '/' . ltrim($logoPath, '/')
                : null,
            'office_name' => $brand['office_name'],
        ];
    }

    public function branding(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatBrandPayload($request),
        ]);
    }

    // =========================================================================
    // 1. AUTENTIKASI & PROFIL (AUTH & PROFILE)
    // =========================================================================

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::with('roles')->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ], 401);
        }

        if (!$user->canAccessMobileApp()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Aplikasi ChronoHR Mobile hanya untuk Karyawan Regular dan Magang. Silakan masuk melalui Portal Web ChronoHR.',
            ], 403);
        }

        $tokenResult = $user->createToken($validated['device_name'] ?? 'flutter-mobile');
        $brandPayload = $this->formatBrandPayload($request);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil masuk ke aplikasi ' . ($brandPayload['app_name'] ?? 'ChronoHR') . ' Mobile.',
            'access_token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $this->formatUserPayload($user),
            'brand' => $brandPayload,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $currentToken = $request->attributes->get('current_access_token');
        if ($currentToken) {
            $currentToken->delete();
        } elseif ($request->user()) {
            $request->user()->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesi Anda telah diakhiri dan token berhasil dihapus.',
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $this->formatUserPayload($user),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi akun berhasil diperbarui.',
        ]);
    }

    // =========================================================================
    // 2. PRESENSI & RIWAYAT KEHADIRAN (ATTENDANCES)
    // =========================================================================

    public function attendanceToday(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $dailyReport = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $officeSettings = $this->attendanceService->getOfficeSettings();

        $hasMorningReport = $dailyReport !== null && !empty($dailyReport->morning_planned_tasks);
        $hasEveningReport = $dailyReport !== null && $dailyReport->status === 'COMPLETED';
        $hasCheckedIn = $attendance !== null && !empty($attendance->time_in);
        $hasCheckedOut = $attendance !== null && !empty($attendance->time_out);

        $todayWfaLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('type', 'WFA')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $hasApprovedWfaToday = $todayWfaLeaves->contains('status', 'APPROVED');
        $wfaRequestStatus = $hasApprovedWfaToday
            ? 'APPROVED'
            : ($todayWfaLeaves->first()?->status);

        $isIntern = $user->employment_type === 'INTERN';
        $canMobileCheckIn = !$hasCheckedIn && ($isIntern || $hasApprovedWfaToday);
        $canOnsiteCheckIn = !$hasCheckedIn && !$isIntern && !$hasApprovedWfaToday;

        $checkoutMode = null;
        if ($hasCheckedIn && !$hasCheckedOut) {
            $checkoutMode = ($isIntern || ($attendance?->status === 'WFA'))
                ? 'MOBILE_FREE'
                : 'RFID_OR_ONSITE_GEOFENCE';
        }

        return response()->json([
            'success' => true,
            'date' => $today,
            'has_checked_in' => $hasCheckedIn,
            'has_checked_out' => $hasCheckedOut,
            'can_checkout' => $hasCheckedIn && !$hasCheckedOut && $hasEveningReport,
            'checkout_mode' => $checkoutMode,
            'has_approved_wfa_today' => $hasApprovedWfaToday,
            'wfa_request_today_status' => $wfaRequestStatus,
            'can_mobile_checkin' => $canMobileCheckIn,
            'can_onsite_checkin' => $canOnsiteCheckIn,
            'attendance' => $attendance ? [
                'id' => $attendance->id,
                'date' => $today,
                'time_in' => $attendance->time_in,
                'time_out' => $attendance->time_out,
                'status' => $attendance->status,
                'method' => $attendance->method,
                'is_late' => (bool) $attendance->is_late,
                'overtime_hours' => (int) $attendance->overtime_hours,
                'latitude' => $attendance->latitude ? (float) $attendance->latitude : null,
                'longitude' => $attendance->longitude ? (float) $attendance->longitude : null,
                'face_image_path' => $attendance->face_image_path,
                'face_image_url' => $attendance->face_image_url,
                'checkout_latitude' => $attendance->checkout_latitude ? (float) $attendance->checkout_latitude : null,
                'checkout_longitude' => $attendance->checkout_longitude ? (float) $attendance->checkout_longitude : null,
                'checkout_face_image_path' => $attendance->checkout_face_image_path,
                'checkout_face_image_url' => $attendance->checkout_face_image_url,
            ] : null,
            'daily_report' => [
                'has_morning_report' => $hasMorningReport,
                'has_evening_report' => $hasEveningReport,
                'status' => $dailyReport?->status,
                'morning_submitted_at' => $dailyReport?->morning_submitted_at,
                'evening_submitted_at' => $dailyReport?->evening_submitted_at,
            ],
            'office' => $officeSettings,
            'work_schedule' => $this->attendanceService->getWorkScheduleSettings(),
        ]);
    }

    public function attendanceWfaCheckIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'presence_type' => 'nullable|in:WFA,WFH,ON_SITE,AUTO',
            'face_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi masuk hari ini pada pukul ' . substr($existing->time_in, 0, 5) . ' WIB.',
                'attendance' => $existing,
            ], 422);
        }

        $todayWfaLeaves = LeaveRequest::where('user_id', $user->id)
            ->where('type', 'WFA')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $hasApprovedWfaToday = $todayWfaLeaves->contains('status', 'APPROVED');
        $latestWfaStatus = $todayWfaLeaves->first()?->status;
        $isIntern = $user->employment_type === 'INTERN';
        $requestedType = strtoupper((string) ($validated['presence_type'] ?? ''));

        // Karyawan (KARTAP/PKWT) tanpa izin WFA APPROVED hanya boleh presensi mobile
        // jika memilih mode ON_SITE (lupa bawa kartu RFID) dengan validasi radius ketat di AttendanceService.
        $isOnsiteForgotRfid = !$isIntern && $requestedType === 'ON_SITE';

        if (!$isIntern && !$hasApprovedWfaToday && !$isOnsiteForgotRfid) {
            $reasonMsg = match ($latestWfaStatus) {
                'PENDING' => 'Presensi WFA/WFH belum dapat dilakukan karena pengajuan WFH Anda hari ini masih menunggu persetujuan (PENDING).',
                'REJECTED' => 'Presensi WFA/WFH ditolak karena pengajuan WFH Anda hari ini berstatus REJECTED. Silakan lakukan TAP RFID atau presensi Lupa RFID di dalam radius kantor.',
                default => 'Presensi WFA/WFH hanya dapat dilakukan jika pengajuan WFH Anda untuk hari ini sudah disetujui (APPROVED). Jika hadir di kantor dan lupa kartu RFID, gunakan mode Presensi Kantor (ON_SITE) di dalam radius kantor.',
            };

            return response()->json([
                'success' => false,
                'wfa_request_today_status' => $latestWfaStatus,
                'message' => $reasonMsg,
            ], 422);
        }

        // Tentukan mode presensi akhir sebelum menyimpan file agar jika radius ditolak (422) tidak menyimpan file yatim.
        if ($isOnsiteForgotRfid) {
            $effectivePresenceType = 'ON_SITE';
        } elseif ($hasApprovedWfaToday) {
            $effectivePresenceType = 'WFA';
        } else {
            // Magang (INTERN) presensi harian via kamera & GPS tanpa batasan radius
            $effectivePresenceType = 'INTERN_MOBILE';
        }

        $result = $this->attendanceService->recordLocationAttendance(
            user: $user,
            latitude: (float) $validated['latitude'],
            longitude: (float) $validated['longitude'],
            presenceType: $effectivePresenceType,
            notes: $validated['notes'] ?? null
        );

        $faceImagePath = null;
        if ($request->hasFile('face_image')) {
            $faceImagePath = $request->file('face_image')->store('faces/' . now()->format('Y-m'), 'public');
        }

        /** @var Attendance $attendance */
        $attendance = $result['attendance'];
        if ($faceImagePath) {
            $attendance->update(['face_image_path' => $faceImagePath]);
            $attendance->refresh();
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'distance_meters' => $result['distance'],
            'is_within_radius' => $result['is_within_radius'],
            'attendance' => $attendance,
        ], 201);
    }

    public function attendanceCheckout(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan presensi masuk hari ini.',
            ], 422);
        }

        if ($attendance->time_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi pulang hari ini pada pukul ' . substr($attendance->time_out, 0, 5) . ' WIB.',
            ], 422);
        }

        // Validasi kunci: DailyReport sore WAJIB sudah berstatus COMPLETED
        $dailyReport = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$dailyReport || $dailyReport->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Anda wajib menyelesaikan evaluasi laporan tugas sore (Daily Report) hingga berstatus COMPLETED sebelum melakukan presensi pulang (Checkout).',
                'daily_report_status' => $dailyReport?->status ?? 'NOT_SUBMITTED',
            ], 422);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'face_image' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];
        $office = $this->attendanceService->getOfficeSettings();
        $distance = $this->attendanceService->calculateDistance(
            $latitude,
            $longitude,
            $office['latitude'],
            $office['longitude']
        );
        $isWithinRadius = $distance <= $office['radius'];

        $isIntern = $user->employment_type === 'INTERN';
        $requiresOfficeGeofence = !$isIntern && $attendance->status === 'HADIR_ON_SITE';

        if ($requiresOfficeGeofence && !$isWithinRadius) {
            return response()->json([
                'success' => false,
                'message' => "Presensi pulang ditolak: Anda tercatat hadir di kantor (On-Site), namun posisi Anda berada di luar radius kantor ({$distance} meter dari kantor, batas {$office['radius']} meter). Silakan TAP kartu RFID di kantor atau lakukan checkout dari dalam area kantor.",
                'errors' => [
                    'location' => [
                        "Posisi Anda berada di luar radius kantor ({$distance} meter dari kantor, batas {$office['radius']} meter).",
                    ],
                ],
                'distance_meters' => $distance,
                'is_within_radius' => false,
            ], 422);
        }

        $checkoutFacePath = $request->file('face_image')->store('faces-checkout/' . now()->format('Y-m'), 'public');

        $now = now();
        $overtimeHours = $this->attendanceService->calculateOvertimeHours($now);

        $attendance->update([
            'time_out' => $now->format('H:i:s'),
            'overtime_hours' => $overtimeHours,
            'checkout_face_image_path' => $checkoutFacePath,
            'checkout_latitude' => $latitude,
            'checkout_longitude' => $longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Presensi pulang berhasil dicatat pada pukul {$now->format('H:i')} WIB." . ($overtimeHours > 0 ? " Lembur tercatat: {$overtimeHours} jam." : ''),
            'overtime_hours' => $overtimeHours,
            'distance_meters' => $distance,
            'is_within_radius' => $isWithinRadius,
            'attendance' => $attendance->fresh(),
        ]);
    }

    public function attendanceHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $records = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $summary = [
            'month' => $month,
            'year' => $year,
            'total_records' => $records->count(),
            'hadir_on_site' => $records->where('status', 'HADIR_ON_SITE')->count(),
            'wfa' => $records->where('status', 'WFA')->count(),
            'sakit' => $records->where('status', 'SAKIT')->count(),
            'izin' => $records->where('status', 'IZIN')->count(),
            'alpha' => $records->where('status', 'ALPHA')->count(),
            'late_count' => $records->where('is_late', true)->count(),
            'total_overtime_hours' => (int) $records->sum('overtime_hours'),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $records,
        ]);
    }

    // =========================================================================
    // 3. DAILY REPORT - TUGAS PAGI & SORE (DAILY REPORTS)
    // =========================================================================

    public function reportToday(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = now()->toDateString();

        $report = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $plannedTasksArray = [];
        if ($report && !empty($report->morning_planned_tasks)) {
            $plannedTasksArray = array_values(array_filter(array_map(function ($line) {
                return trim(preg_replace('/^(\d+[\.\)]\s*|[-*•]\s*)/', '', trim($line)));
            }, preg_split('/\r?\n/', $report->morning_planned_tasks))));
        }

        return response()->json([
            'success' => true,
            'date' => $today,
            'has_submitted_morning' => $report !== null,
            'is_completed' => $report?->status === 'COMPLETED',
            'morning_tasks_list' => $plannedTasksArray,
            'data' => $report,
        ]);
    }

    public function reportMorning(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'morning_planned_tasks' => 'required',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus melakukan presensi masuk terlebih dahulu sebelum mengirim rencana tugas pagi.',
            ], 422);
        }

        $existingReport = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($existingReport && $existingReport->status === 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Laporan harian hari ini sudah selesai (COMPLETED) dan tidak dapat ditimpa ulang dengan laporan pagi.',
            ], 422);
        }

        $tasksText = is_array($validated['morning_planned_tasks'])
            ? implode("\n", array_map('trim', $validated['morning_planned_tasks']))
            : trim((string) $validated['morning_planned_tasks']);

        $report = DailyReport::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $today,
            ],
            [
                'morning_submitted_at' => now()->format('H:i:s'),
                'morning_planned_tasks' => $tasksText,
                'status' => 'MORNING_ONLY',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Rencana tugas pagi berhasil disimpan.',
            'data' => $report,
        ], 201);
    }

    public function reportEvening(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'evening_completed_tasks' => 'nullable',
            'evening_pending_tasks' => 'nullable',
            'evening_additional_tasks' => 'nullable',
        ]);

        $user = $request->user();
        $today = now()->toDateString();

        $report = DailyReport::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum mengisi rencana tugas pagi hari ini. Silakan kirim laporan pagi terlebih dahulu.',
            ], 422);
        }

        $completedText = is_array($validated['evening_completed_tasks'] ?? null)
            ? implode("\n", array_map('trim', $validated['evening_completed_tasks']))
            : trim((string) ($validated['evening_completed_tasks'] ?? ''));

        $pendingText = is_array($validated['evening_pending_tasks'] ?? null)
            ? implode("\n", array_map('trim', $validated['evening_pending_tasks']))
            : trim((string) ($validated['evening_pending_tasks'] ?? ''));

        $additionalText = is_array($validated['evening_additional_tasks'] ?? null)
            ? implode("\n", array_map('trim', $validated['evening_additional_tasks']))
            : trim((string) ($validated['evening_additional_tasks'] ?? ''));

        if ($completedText === '' && $pendingText === '') {
            return response()->json([
                'success' => false,
                'message' => 'Harap isi setidaknya daftar tugas yang selesai atau tugas yang tertunda.',
            ], 422);
        }

        $report->update([
            'evening_submitted_at' => now()->format('H:i:s'),
            'evening_completed_tasks' => $completedText !== '' ? $completedText : null,
            'evening_pending_tasks' => $pendingText !== '' ? $pendingText : null,
            'evening_additional_tasks' => $additionalText !== '' ? $additionalText : null,
            'status' => 'COMPLETED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi tugas sore berhasil disimpan. Akses presensi pulang (Checkout) kini terbuka.',
            'can_checkout' => true,
            'data' => $report->fresh(),
        ]);
    }

    public function reportHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $month = $request->input('month');
        $year = $request->input('year');

        $query = DailyReport::where('user_id', $user->id)->orderBy('date', 'desc');

        if ($month) {
            $query->whereMonth('date', (int) $month);
        }
        if ($year) {
            $query->whereYear('date', (int) $year);
        }

        $reports = $query->limit(60)->get();

        return response()->json([
            'success' => true,
            'total' => $reports->count(),
            'data' => $reports,
        ]);
    }

    // =========================================================================
    // 4. MANAJEMEN PERIZINAN & DYNAMIC APPROVAL (LEAVE REQUESTS)
    // =========================================================================

    public function leavesIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        $leaves = LeaveRequest::with('approver:id,name,email')
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'total' => $leaves->count(),
            'data' => $leaves,
        ]);
    }

    public function leavesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:SAKIT,IZIN,WFA,WFH',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = $request->user();
        $type = $validated['type'] === 'WFH' ? 'WFA' : $validated['type'];

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments/' . now()->format('Y-m'), 'public');
        }

        $leave = LeaveRequest::create([
            'user_id' => $user->id,
            'type' => $type,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pengajuan {$type} berhasil dikirim dan menunggu persetujuan atasan.",
            'data' => $leave,
        ], 201);
    }

    public function leavesApprovals(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->can_approve_leaves && !$user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk menyetujui permohonan izin/WFA.',
            ], 403);
        }

        $statusFilter = strtoupper((string) $request->input('status', 'PENDING'));
        $query = LeaveRequest::with([
                'user:id,name,email,employment_type',
                'approver:id,name,email',
            ])
            ->where('user_id', '!=', $user->id)
            ->orderBy('created_at', 'desc');

        if (in_array($statusFilter, ['PENDING', 'APPROVED', 'REJECTED'], true)) {
            $query->where('status', $statusFilter);
        }

        $leaves = $query->get();

        return response()->json([
            'success' => true,
            'filter_status' => in_array($statusFilter, ['PENDING', 'APPROVED', 'REJECTED', 'ALL'], true) ? $statusFilter : 'PENDING',
            'total_pending' => LeaveRequest::where('user_id', '!=', $user->id)->where('status', 'PENDING')->count(),
            'total' => $leaves->count(),
            'data' => $leaves,
        ]);
    }

    public function leavesAction(Request $request, LeaveRequest $leaveRequest): JsonResponse
    {
        $user = $request->user();

        if (!$user->can_approve_leaves && !$user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk memproses persetujuan pengajuan ini.',
            ], 403);
        }

        if ($leaveRequest->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menyetujui atau menolak pengajuan perizinan milik Anda sendiri.',
            ], 403);
        }

        if ($leaveRequest->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => "Pengajuan ini sudah diproses sebelumnya dengan status {$leaveRequest->status}.",
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'rejection_reason' => 'required_if:status,REJECTED|nullable|string|max:500',
        ]);

        if ($validated['status'] === 'APPROVED') {
            $leaveRequest->approve($user);
            $message = "Pengajuan {$leaveRequest->type} disetujui dan tabel presensi otomatis diperbarui.";
        } else {
            $leaveRequest->reject($user, $validated['rejection_reason'] ?? 'Ditolak oleh penyetuju.');
            $message = "Pengajuan {$leaveRequest->type} telah ditolak.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $leaveRequest->fresh(['user:id,name,email', 'approver:id,name,email']),
        ]);
    }

    // =========================================================================
    // 5. FINANSIAL: SLIP GAJI & INSENTIF PROYEK (PAYROLLS & INCENTIVES)
    // =========================================================================

    public function payrollsIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->employment_type === 'INTERN') {
            return response()->json([
                'success' => false,
                'message' => 'Menu slip gaji bulanan tidak tersedia untuk status Magang/INTERN. Silakan gunakan menu Insentif Proyek.',
            ], 403);
        }

        $payrolls = Payroll::where('user_id', $user->id)
            ->where('status', 'PUBLISHED')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function (Payroll $p) {
                $monthName = Carbon::create($p->year, $p->month, 1)->translatedFormat('F');
                return [
                    'id' => $p->id,
                    'month' => $p->month,
                    'year' => $p->year,
                    'period_label' => "{$monthName} {$p->year}",
                    'total_working_days' => $p->total_working_days,
                    'total_present_onsite' => $p->total_present_onsite,
                    'total_wfa_days' => $p->total_wfa_days,
                    'total_overtime_hours' => $p->total_overtime_hours,
                    'gross_salary' => (float) $p->gross_salary,
                    'wfa_deduction_amount' => (float) $p->wfa_deduction_amount,
                    'leave_deduction_amount' => (float) ($p->leave_deduction_amount ?? 0),
                    'overtime_pay' => (float) $p->overtime_pay,
                    'net_salary' => (float) $p->net_salary,
                    'status' => $p->status,
                    'download_url' => url("/api/payrolls/{$p->id}/download"),
                ];
            });

        return response()->json([
            'success' => true,
            'employment_type' => $user->employment_type,
            'total' => $payrolls->count(),
            'data' => $payrolls,
        ]);
    }

    public function payrollsShow(Request $request, Payroll $payroll): JsonResponse
    {
        $user = $request->user();
        $isPrivileged = $user->hasRole('super_admin') || $user->hasRole('finance');

        if ($user->employment_type === 'INTERN' && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Menu slip gaji bulanan tidak tersedia untuk status Magang/INTERN.',
            ], 403);
        }

        if ($payroll->user_id !== $user->id && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melihat slip gaji ini.',
            ], 403);
        }

        if ($payroll->status !== 'PUBLISHED' && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Slip gaji ini belum diterbitkan (masih berstatus DRAFT).',
            ], 403);
        }

        $payroll->loadMissing('user');
        $owner = $payroll->user;
        $monthName = Carbon::create($payroll->year, $payroll->month, 1)->translatedFormat('F');
        $kartapPct = (int) Setting::getValue('wfa_deduction_kartap_pct', 10);
        $pkwtPct = (int) Setting::getValue('wfa_deduction_pkwt_pct', 30);
        $activeWfaPct = ($owner->employment_type ?? '') === 'PKWT' ? $pkwtPct : $kartapPct;

        $projectIncentives = (float) ProjectIncentive::where('user_id', $owner->id)
            ->whereMonth('given_date', $payroll->month)
            ->whereYear('given_date', $payroll->year)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payroll->id,
                'document_number' => 'CHR-PAY/' . $payroll->year . sprintf('%02d', $payroll->month) . '/' . strtoupper(substr($payroll->id, 0, 8)),
                'month' => $payroll->month,
                'year' => $payroll->year,
                'period_label' => "{$monthName} {$payroll->year}",
                'status' => $payroll->status,
                'employee' => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                    'employment_type' => $owner->employment_type,
                    'bank_name' => $owner->bank_name,
                    'bank_account_number' => $owner->bank_account_number,
                    'bank_account_holder' => $owner->bank_account_holder ?? $owner->name,
                ],
                'attendance_summary' => [
                    'total_working_days' => $payroll->total_working_days,
                    'total_present_onsite' => $payroll->total_present_onsite,
                    'total_wfa_days' => $payroll->total_wfa_days,
                    'total_sick_days' => $payroll->total_sick_days ?? 0,
                    'total_permit_days' => $payroll->total_permit_days ?? 0,
                    'total_overtime_hours' => $payroll->total_overtime_hours,
                ],
                'earnings' => [
                    'basic_salary' => (float) ($owner->basic_salary ?? 0),
                    'daily_salary_rate' => (float) $payroll->daily_salary_rate,
                    'overtime_pay' => (float) $payroll->overtime_pay,
                    'project_incentives' => $projectIncentives,
                    'gross_salary' => (float) $payroll->gross_salary,
                ],
                'deductions' => [
                    'wfa_deduction_pct' => $activeWfaPct,
                    'wfa_deduction_amount' => (float) $payroll->wfa_deduction_amount,
                    'leave_deduction_amount' => (float) ($payroll->leave_deduction_amount ?? 0),
                    'sanction_deduction_amount' => (float) ($payroll->sanction_deduction_amount ?? 0),
                    'total_deduction_amount' => (float) ($payroll->wfa_deduction_amount + ($payroll->leave_deduction_amount ?? 0) + ($payroll->sanction_deduction_amount ?? 0)),
                ],
                'net_salary' => (float) $payroll->net_salary,
                'terbilang' => NumberHelper::terbilang($payroll->net_salary),
                'download_url' => url("/api/payrolls/{$payroll->id}/download"),
            ],
        ]);
    }

    public function payrollsDownload(Request $request, Payroll $payroll): Response|JsonResponse
    {
        $user = $request->user();
        $isPrivileged = $user->hasRole('super_admin') || $user->hasRole('finance');

        if ($user->employment_type === 'INTERN' && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Menu slip gaji bulanan tidak tersedia untuk status Magang/INTERN.',
            ], 403);
        }

        if ($payroll->user_id !== $user->id && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengunduh slip gaji ini.',
            ], 403);
        }

        if ($payroll->status !== 'PUBLISHED' && !$isPrivileged) {
            return response()->json([
                'success' => false,
                'message' => 'Slip gaji ini belum diterbitkan (masih berstatus DRAFT).',
            ], 403);
        }

        $payroll->loadMissing('user');
        $owner = $payroll->user;
        $monthName = Carbon::create($payroll->year, $payroll->month, 1)->translatedFormat('F');
        $terbilang = NumberHelper::terbilang($payroll->net_salary);
        $projectIncentives = (float) ProjectIncentive::where('user_id', $owner->id)
            ->whereMonth('given_date', $payroll->month)
            ->whereYear('given_date', $payroll->year)
            ->sum('amount');

        $officeSettings = [
            'name' => Setting::getValue('office_name', 'PT ChronoHR Teknologi Indonesia'),
            'address' => Setting::getValue('office_address', 'Gedung Pusat ChronoHR, Jakarta'),
            'payday' => Setting::getValue('payday_date', 25),
            'wfa_kartap_pct' => Setting::getValue('wfa_deduction_kartap_pct', 10),
            'wfa_pkwt_pct' => Setting::getValue('wfa_deduction_pkwt_pct', 30),
        ];

        $pdf = Pdf::loadView('reports.slip_pdf', [
            'payroll' => $payroll,
            'user' => $owner,
            'monthName' => $monthName,
            'terbilang' => $terbilang,
            'projectIncentives' => $projectIncentives,
            'officeSettings' => $officeSettings,
        ])->setPaper('a4', 'portrait');

        $cleanUserName = preg_replace('/[^A-Za-z0-9_-]/', '_', $owner->name ?? 'Karyawan');
        $filename = "Slip_Gaji_ChronoHR_{$cleanUserName}_{$monthName}_{$payroll->year}.pdf";

        return $pdf->download($filename);
    }

    public function incentivesIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        $incentives = ProjectIncentive::where('user_id', $user->id)
            ->orderBy('given_date', 'desc')
            ->get()
            ->map(fn (ProjectIncentive $inc) => [
                'id' => $inc->id,
                'project_name' => $inc->project_name,
                'amount' => (float) $inc->amount,
                'given_date' => $inc->given_date,
                'notes' => $inc->notes,
            ]);

        return response()->json([
            'success' => true,
            'employment_type' => $user->employment_type,
            'total_count' => $incentives->count(),
            'total_amount' => (float) $incentives->sum('amount'),
            'data' => $incentives,
        ]);
    }

    // =========================================================================
    // 6. SANKSI & EVALUASI KEDISIPLINAN (SANCTIONS)
    // =========================================================================

    public function sanctionsIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        $sanctions = EmployeeSanction::with('rule:id,code,name,category')
            ->where('user_id', $user->id)
            ->orderBy('incident_date', 'desc')
            ->get()
            ->map(fn (EmployeeSanction $s) => [
                'id' => $s->id,
                'month' => $s->month,
                'year' => $s->year,
                'incident_date' => $s->incident_date,
                'violation_name' => $s->violation_name,
                'level' => $s->level,
                'level_label' => SanctionRule::levelLabel($s->level),
                'source' => $s->source,
                'occurrence_count' => $s->occurrence_count,
                'deduction_amount' => (float) $s->deduction_amount,
                'reason' => $s->reason,
                'status' => $s->status,
            ]);

        $currentMonthLateCount = Attendance::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('is_late', true)
            ->count();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_sanctions' => $sanctions->count(),
                'active_sanctions' => $sanctions->where('status', 'ACTIVE')->count(),
                'current_month_late_count' => $currentMonthLateCount,
            ],
            'data' => $sanctions,
        ]);
    }

    public function sanctionsShow(Request $request, EmployeeSanction $employeeSanction): JsonResponse
    {
        $user = $request->user();

        if ($employeeSanction->user_id !== $user->id && !$user->hasRole('super_admin') && !$user->hasRole('finance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melihat rincian sanksi ini.',
            ], 403);
        }

        $employeeSanction->loadMissing(['rule:id,code,name,category', 'issuer:id,name']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $employeeSanction->id,
                'month' => $employeeSanction->month,
                'year' => $employeeSanction->year,
                'incident_date' => $employeeSanction->incident_date,
                'violation_name' => $employeeSanction->violation_name,
                'level' => $employeeSanction->level,
                'level_label' => SanctionRule::levelLabel($employeeSanction->level),
                'source' => $employeeSanction->source,
                'occurrence_count' => $employeeSanction->occurrence_count,
                'deduction_amount' => (float) $employeeSanction->deduction_amount,
                'reason' => $employeeSanction->reason,
                'status' => $employeeSanction->status,
                'rule' => $employeeSanction->rule,
                'issued_by' => $employeeSanction->issuer?->name ?? 'Sistem Otomatis ChronoHR',
                'download_url' => url("/api/sanctions/{$employeeSanction->id}/download"),
            ],
        ]);
    }

    public function sanctionsDownload(Request $request, EmployeeSanction $employeeSanction): Response|JsonResponse
    {
        $user = $request->user();

        if ($employeeSanction->user_id !== $user->id && !$user->hasRole('super_admin') && !$user->hasRole('finance')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengunduh surat sanksi ini.',
            ], 403);
        }

        $employeeSanction->loadMissing(['user', 'rule', 'issuer']);
        $owner = $employeeSanction->user;

        $officeSettings = [
            'name' => Setting::getValue('office_name', 'PT ChronoHR Teknologi Indonesia'),
            'address' => Setting::getValue('office_address', 'Gedung Pusat ChronoHR, Jakarta'),
        ];

        $pdf = Pdf::loadView('sanctions.sp_pdf', [
            'sanction' => $employeeSanction,
            'user' => $owner,
            'officeSettings' => $officeSettings,
        ])->setPaper('a4', 'portrait');

        $cleanName = preg_replace('/[^A-Za-z0-9_-]/', '_', $owner->name ?? 'Karyawan');
        $filename = "Surat_Sanksi_{$employeeSanction->level}_{$cleanName}_{$employeeSanction->month}_{$employeeSanction->year}.pdf";

        return $pdf->download($filename);
    }
}
