<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $users = User::where('email', '!=', 'admin@test.com')->get();

        if ($users->isEmpty()) {
            return;
        }

        $sampleLeaves = [
            [
                'email' => 'dewi@chrono.id',
                'type' => 'SAKIT',
                'start_date' => '2026-10-05',
                'end_date' => '2026-10-06',
                'reason' => 'Demam tinggi dan flu, istirahat dokter 2 hari.',
                'status' => 'APPROVED',
                'approved_by' => $admin?->id,
            ],
            [
                'email' => 'dimas@chrono.id',
                'type' => 'WFH',
                'start_date' => '2026-10-12',
                'end_date' => '2026-10-13',
                'reason' => 'Perbaikan instalasi jaringan rumah & remote coding sprint.',
                'status' => 'APPROVED',
                'approved_by' => $admin?->id,
            ],
            [
                'email' => 'fajar@chrono.id',
                'type' => 'IZIN',
                'start_date' => '2026-10-15',
                'end_date' => '2026-10-15',
                'reason' => 'Urusan administrasi perbankan dan kependudukan keluarga.',
                'status' => 'APPROVED',
                'approved_by' => $admin?->id,
            ],
            [
                'email' => 'maya@chrono.id',
                'type' => 'SAKIT',
                'start_date' => '2026-10-18',
                'end_date' => '2026-10-19',
                'reason' => 'Migrain akut dan rekomendasi istirahat klinik.',
                'status' => 'PENDING',
                'approved_by' => null,
            ],
            [
                'email' => 'karyawan@test.com',
                'type' => 'WFH',
                'start_date' => '2026-10-20',
                'end_date' => '2026-10-21',
                'reason' => 'Menjaga anggota keluarga yang sedang opname di rumah sakit.',
                'status' => 'PENDING',
                'approved_by' => null,
            ],
            [
                'email' => 'intern@test.com',
                'type' => 'IZIN',
                'start_date' => '2026-10-02',
                'end_date' => '2026-10-02',
                'reason' => 'Kegiatan bimbingan akademik skripsi di kampus.',
                'status' => 'REJECTED',
                'approved_by' => $admin?->id,
                'rejection_reason' => 'Harap konfirmasi terlebih dahulu dengan pembimbing proyek minimal H-2.',
            ],
        ];

        foreach ($sampleLeaves as $data) {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                LeaveRequest::create([
                    'user_id' => $user->id,
                    'type' => $data['type'],
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'reason' => $data['reason'],
                    'status' => $data['status'],
                    'approved_by' => $data['approved_by'],
                    'rejection_reason' => $data['rejection_reason'] ?? null,
                ]);
            }
        }
    }
}
