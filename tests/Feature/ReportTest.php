<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    public function test_super_admin_can_access_attendance_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.attendances'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Presensi');
        $response->assertSee('Menu Laporan');
        $response->assertSee('Slip Gaji');
        $response->assertSee('Pengajuan');
    }

    public function test_super_admin_can_access_payroll_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.payrolls'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Slip Gaji');
        $response->assertSee('Rincian Slip Penggajian Karyawan');
    }

    public function test_super_admin_can_access_leave_report(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('reports.leaves'));

        $response->assertOk();
        $response->assertSee('Laporan Rekapitulasi Pengajuan');
        $response->assertSee('Komposisi Pengajuan');
    }

    public function test_finance_role_can_access_reports(): void
    {
        $finance = User::where('email', 'finance@test.com')->first();

        $response = $this->actingAs($finance)->get(route('reports.attendances'));
        $response->assertOk();

        $response = $this->actingAs($finance)->get(route('reports.payrolls'));
        $response->assertOk();

        $response = $this->actingAs($finance)->get(route('reports.leaves'));
        $response->assertOk();
    }

    public function test_regular_karyawan_cannot_access_reports(): void
    {
        $karyawan = User::where('email', 'karyawan@test.com')->first();

        $response = $this->actingAs($karyawan)->get(route('reports.attendances'));
        $response->assertForbidden();

        $response = $this->actingAs($karyawan)->get(route('reports.payrolls'));
        $response->assertForbidden();

        $response = $this->actingAs($karyawan)->get(route('reports.leaves'));
        $response->assertForbidden();
    }
}
