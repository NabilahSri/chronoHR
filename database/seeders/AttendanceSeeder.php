<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            return;
        }

        $dates = [
            Carbon::create(2026, 10, 19),
            Carbon::create(2026, 10, 18),
            Carbon::create(2026, 10, 17),
            Carbon::create(2026, 10, 16),
            Carbon::create(2026, 10, 15),
        ];

        foreach ($dates as $date) {
            $formattedDate = $date->toDateString();

            foreach ($users as $index => $user) {
                // Check if already seeded
                if (Attendance::where('user_id', $user->id)->where('date', $formattedDate)->exists()) {
                    continue;
                }

                $mod = ($index + $date->day) % 6;

                if ($mod === 0) {
                    // Hadir On-Site Tepat Waktu (RFID)
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $formattedDate,
                        'time_in' => '07:48:22',
                        'time_out' => '17:05:10',
                        'status' => 'HADIR_ON_SITE',
                        'method' => 'RFID',
                        'is_late' => false,
                        'overtime_hours' => 0,
                    ]);
                } elseif ($mod === 1) {
                    // Hadir On-Site dengan Lembur (RFID)
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $formattedDate,
                        'time_in' => '07:55:00',
                        'time_out' => '19:30:15',
                        'status' => 'HADIR_ON_SITE',
                        'method' => 'RFID',
                        'is_late' => false,
                        'overtime_hours' => 2,
                    ]);
                } elseif ($mod === 2) {
                    // Hadir On-Site Terlambat
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $formattedDate,
                        'time_in' => '08:35:40',
                        'time_out' => '17:10:00',
                        'status' => 'HADIR_ON_SITE',
                        'method' => 'RFID',
                        'is_late' => true,
                        'overtime_hours' => 0,
                    ]);
                } elseif ($mod === 3) {
                    // Work From Home (Face & GPS)
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $formattedDate,
                        'time_in' => '08:00:12',
                        'time_out' => '17:02:45',
                        'status' => 'WFH',
                        'method' => 'FACIAL_LOCATION',
                        'is_late' => false,
                        'overtime_hours' => 1,
                    ]);
                } elseif ($mod === 4) {
                    // Izin / Sakit via Form
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $formattedDate,
                        'time_in' => null,
                        'time_out' => null,
                        'status' => $date->day % 2 === 0 ? 'SAKIT' : 'IZIN',
                        'method' => 'FORM',
                        'is_late' => false,
                        'overtime_hours' => 0,
                    ]);
                }
            }
        }
    }
}
