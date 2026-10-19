<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LocationAttendanceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Set acuan koordinat kantor standar untuk pengujian
        Setting::updateOrCreate(['key' => 'office_latitude'], ['value' => '-6.20880000']);
        Setting::updateOrCreate(['key' => 'office_longitude'], ['value' => '106.84560000']);
        Setting::updateOrCreate(['key' => 'office_radius_meters'], ['value' => '100']);
        Setting::updateOrCreate(['key' => 'late_tolerance_minutes'], ['value' => '15']);
        Setting::updateOrCreate(['key' => 'overtime_tolerance_minutes'], ['value' => '30']);
    }

    public function test_office_location_settings_can_be_configured_by_super_admin(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));
        $response->assertOk();
        $response->assertSee('Titik Acuan Lokasi Kantor');
        $response->assertSee('Geofencing Presensi');
        $response->assertSee('office_latitude');
        $response->assertSee('office_radius_meters');

        // Update koordinat kantor
        $updateResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'late_tolerance_minutes' => 15,
            'overtime_tolerance_minutes' => 30,
            'wfh_deduction_kartap_pct' => 10,
            'wfh_deduction_pkwt_pct' => 30,
            'payday_date' => 25,
            'office_name' => 'Menara Chrono Sudirman',
            'office_address' => 'Jl. Jenderal Sudirman No. 1, Jakarta',
            'office_latitude' => -6.21000000,
            'office_longitude' => 106.84000000,
            'office_radius_meters' => 150,
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('-6.21', substr(Setting::getValue('office_latitude'), 0, 5));
        $this->assertEquals('150', Setting::getValue('office_radius_meters'));
        $this->assertEquals('Menara Chrono Sudirman', Setting::getValue('office_name'));
    }

    public function test_intern_records_attendance_using_location_within_office_radius(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $intern = User::where('employment_type', 'INTERN')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $intern->id)->whereDate('date', now()->toDateString())->delete();

        // Koordinat persis di kantor (jarak 0 meter)
        $response = $this->actingAs($admin)->post(route('attendances.location-check-in'), [
            'user_id' => $intern->id,
            'latitude' => -6.20880000,
            'longitude' => 106.84560000,
            'presence_type' => 'ON_SITE',
            'notes' => 'Presensi magang di kantor',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $intern->id,
            'date' => now()->toDateString(),
            'status' => 'HADIR_ON_SITE',
            'method' => 'FACIAL_LOCATION',
        ]);
    }

    public function test_intern_records_attendance_remotely(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $intern = User::where('employment_type', 'INTERN')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $intern->id)->whereDate('date', now()->toDateString())->delete();

        // Koordinat jauh dari kantor (misal di Bandung: -6.9175, 107.6191)
        $response = $this->actingAs($admin)->post(route('attendances.location-check-in'), [
            'user_id' => $intern->id,
            'latitude' => -6.91750000,
            'longitude' => 107.61910000,
            'presence_type' => 'WFH',
            'notes' => 'Magang kerja remote WFH',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $intern->id,
            'date' => now()->toDateString(),
            'status' => 'WFH',
            'method' => 'FACIAL_LOCATION',
        ]);
    }

    public function test_employee_records_attendance_for_wfh_using_location(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('employment_type', 'KARTAP')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $employee->id)->whereDate('date', now()->toDateString())->delete();

        // Karyawan WFH di luar kantor
        $response = $this->actingAs($admin)->post(route('attendances.location-check-in'), [
            'user_id' => $employee->id,
            'latitude' => -6.30000000,
            'longitude' => 106.80000000,
            'presence_type' => 'WFH',
            'notes' => 'WFH disetujui',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'WFH',
            'method' => 'FACIAL_LOCATION',
        ]);
    }

    public function test_employee_forgets_rfid_records_onsite_attendance_within_office_radius(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('employment_type', 'PKWT')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $employee->id)->whereDate('date', now()->toDateString())->delete();

        // Koordinat berada di dalam radius kantor (jarak sekitar 20 meter: -6.20885, 106.84565)
        $response = $this->actingAs($admin)->post(route('attendances.location-check-in'), [
            'user_id' => $employee->id,
            'latitude' => -6.20885000,
            'longitude' => 106.84565000,
            'presence_type' => 'ON_SITE',
            'notes' => 'Lupa bawa kartu RFID',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'HADIR_ON_SITE',
            'method' => 'FACIAL_LOCATION',
        ]);
    }

    public function test_employee_forgets_rfid_onsite_attendance_rejected_when_outside_radius(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $employee = User::where('employment_type', 'PKWT')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $employee->id)->whereDate('date', now()->toDateString())->delete();

        // Koordinat berada di luar radius kantor (misal 5 kilometer jauhnya)
        $response = $this->actingAs($admin)->post(route('attendances.location-check-in'), [
            'user_id' => $employee->id,
            'latitude' => -6.25000000,
            'longitude' => 106.80000000,
            'presence_type' => 'ON_SITE',
            'notes' => 'Coba presensi on site tapi di luar kantor',
        ]);

        $response->assertSessionHasErrors('location');

        $this->assertDatabaseMissing('attendances', [
            'user_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'HADIR_ON_SITE',
        ]);
    }

    public function test_api_location_check_in_and_check_out_flow(): void
    {
        $intern = User::where('employment_type', 'INTERN')->first();

        // Bersihkan data presensi hari ini jika sudah ada dari seeder
        Attendance::where('user_id', $intern->id)->whereDate('date', now()->toDateString())->delete();

        // 1. Check-in via API
        $responseIn = $this->postJson(route('api.attendances.location-check-in'), [
            'email' => $intern->email,
            'latitude' => -6.20880000,
            'longitude' => 106.84560000,
            'presence_type' => 'ON_SITE',
            'notes' => 'Check-in API Magang',
        ]);

        $responseIn->assertOk();
        $responseIn->assertJson([
            'success' => true,
            'action' => 'check_in',
            'is_within_radius' => true,
        ]);

        // 2. Check-out via API
        $responseOut = $this->postJson(route('api.attendances.location-check-in'), [
            'email' => $intern->email,
            'latitude' => -6.20880000,
            'longitude' => 106.84560000,
            'presence_type' => 'ON_SITE',
            'notes' => 'Check-out API Magang',
        ]);

        $responseOut->assertOk();
        $responseOut->assertJson([
            'success' => true,
            'action' => 'check_out',
        ]);

        $attendance = Attendance::where('user_id', $intern->id)->whereDate('date', now()->toDateString())->first();
        $this->assertNotNull($attendance->time_in);
        $this->assertNotNull($attendance->time_out);
    }
}
