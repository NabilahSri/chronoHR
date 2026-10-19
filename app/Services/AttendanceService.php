<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    /**
     * Hitung jarak antara dua koordinat latitude/longitude menggunakan rumus Haversine (hasil dalam meter).
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 2);
    }

    /**
     * Ambil koordinat acuan kantor dan radius geofence dari Setting.
     */
    public function getOfficeSettings(): array
    {
        return [
            'name' => Setting::getValue('office_name', 'Kantor Pusat ChronoHR'),
            'address' => Setting::getValue('office_address', 'Sudirman Central Business District (SCBD) Lot 28, Senayan, Jakarta Selatan'),
            'latitude' => (float) Setting::getValue('office_latitude', -6.20880000),
            'longitude' => (float) Setting::getValue('office_longitude', 106.84560000),
            'radius' => (int) Setting::getValue('office_radius_meters', 100),
        ];
    }

    /**
     * Catat presensi berbasis lokasi GPS:
     * - Magang: wajib via location.
     * - Karyawan WFH: via location.
     * - Karyawan On-Site Lupa Bawa RFID: via location dengan validasi radius kantor.
     *
     * @param User $user
     * @param float $latitude
     * @param float $longitude
     * @param string $presenceType ('ON_SITE', 'WFH', 'AUTO')
     * @param string|null $notes
     * @param Carbon|null $timestamp
     * @return array
     * @throws ValidationException
     */
    public function recordLocationAttendance(
        User $user,
        float $latitude,
        float $longitude,
        string $presenceType = 'AUTO',
        ?string $notes = null,
        ?Carbon $timestamp = null
    ): array {
        $now = $timestamp ? $timestamp->copy() : now();
        $today = $now->toDateString();
        $timeString = $now->format('H:i:s');

        $office = $this->getOfficeSettings();
        $distance = $this->calculateDistance($latitude, $longitude, $office['latitude'], $office['longitude']);
        $isWithinRadius = ($distance <= $office['radius']);

        $isIntern = ($user->employment_type === 'INTERN');

        // Penentuan Status Kehadiran (HADIR_ON_SITE vs WFH)
        $determinedStatus = 'HADIR_ON_SITE';

        if ($isIntern) {
            // Anak magang presensi menggunakan location
            if ($presenceType === 'WFH') {
                $determinedStatus = 'WFH';
            } elseif ($presenceType === 'ON_SITE') {
                if (!$isWithinRadius) {
                    throw ValidationException::withMessages([
                        'location' => "Presensi On-Site ditolak: Anda terdeteksi berada di luar area kantor ({$distance} meter dari kantor, batas radius {$office['radius']} meter). Silakan lakukan presensi saat tiba di kantor atau pilih WFH.",
                    ]);
                }
                $determinedStatus = 'HADIR_ON_SITE';
            } else {
                // Mode AUTO
                $determinedStatus = $isWithinRadius ? 'HADIR_ON_SITE' : 'WFH';
            }
        } else {
            // Karyawan (KARTAP / PKWT)
            if ($presenceType === 'WFH') {
                $determinedStatus = 'WFH';
            } elseif ($presenceType === 'ON_SITE') {
                // Karyawan lupa bawa kartu RFID dan hadir di kantor
                if (!$isWithinRadius) {
                    throw ValidationException::withMessages([
                        'location' => "Presensi On-Site ditolak: Posisi Anda berada di luar radius kantor ({$distance} meter dari kantor, batas toleransi {$office['radius']} meter). Pastikan Anda berada di area kantor atau pilih opsi WFH jika bekerja dari rumah.",
                    ]);
                }
                $determinedStatus = 'HADIR_ON_SITE';
            } else {
                // Mode AUTO
                if ($isWithinRadius) {
                    $determinedStatus = 'HADIR_ON_SITE';
                } else {
                    $determinedStatus = 'WFH';
                }
            }
        }

        // Cek record kehadiran hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // Parameter Jam Kerja & Toleransi
        $lateToleranceMinutes = (int) Setting::getValue('late_tolerance_minutes', 15);
        $overtimeToleranceMinutes = (int) Setting::getValue('overtime_tolerance_minutes', 30);

        if (!$attendance) {
            // --- PROSES CHECK-IN (TAP MASUK) ---
            $standardIn = Carbon::parse($today . ' 08:00:00');
            $lateCutoff = $standardIn->copy()->addMinutes($lateToleranceMinutes);
            $isLate = $now->greaterThan($lateCutoff);

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'time_in' => $timeString,
                'status' => $determinedStatus,
                'method' => 'FACIAL_LOCATION',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'is_late' => $isLate,
                'overtime_hours' => 0,
            ]);

            $typeLabel = $isIntern ? 'Magang' : ($determinedStatus === 'HADIR_ON_SITE' ? 'Karyawan (Lupa RFID)' : 'Karyawan WFH');
            $message = "Check-In {$typeLabel} berhasil dicatat pada {$timeString} WIB. Jarak ke kantor: {$distance}m (" . ($isWithinRadius ? 'Dalam Radius' : 'Luar Radius / WFH') . ").";

            return [
                'status' => 'success',
                'action' => 'check_in',
                'attendance' => $attendance,
                'distance' => $distance,
                'is_within_radius' => $isWithinRadius,
                'message' => $message,
            ];
        }

        if ($attendance->time_out === null) {
            // --- PROSES CHECK-OUT (TAP PULANG) ---
            $standardOut = Carbon::parse($today . ' 17:00:00');
            $overtimeThreshold = $standardOut->copy()->addMinutes($overtimeToleranceMinutes);

            $overtimeHours = 0;
            if ($now->greaterThan($overtimeThreshold)) {
                $diffMinutes = $standardOut->diffInMinutes($now, false);
                $overtimeHours = max(0, (int) floor($diffMinutes / 60));
            }

            $attendance->update([
                'time_out' => $timeString,
                'overtime_hours' => $overtimeHours,
            ]);

            $message = "Check-Out berhasil dicatat pada {$timeString} WIB." . ($overtimeHours > 0 ? " Lembur dihitung: {$overtimeHours} jam." : '');

            return [
                'status' => 'success',
                'action' => 'check_out',
                'attendance' => $attendance,
                'distance' => $distance,
                'is_within_radius' => $isWithinRadius,
                'message' => $message,
            ];
        }

        // Sudah pernah check-in dan check-out hari ini
        throw ValidationException::withMessages([
            'attendance' => "Anda sudah menyelesaikan presensi masuk ({$attendance->time_in}) dan pulang ({$attendance->time_out}) untuk hari ini ({$today}).",
        ]);
    }
}
