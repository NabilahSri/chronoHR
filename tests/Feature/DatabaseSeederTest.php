<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_database_seeder_has_all_modular_data_present(): void
    {
        // Verify Roles
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);
        $this->assertDatabaseHas('roles', ['name' => 'ceo']);
        $this->assertDatabaseHas('roles', ['name' => 'finance']);
        $this->assertDatabaseHas('roles', ['name' => 'karyawan']);
        $this->assertDatabaseHas('roles', ['name' => 'intern']);

        // Verify Settings
        $this->assertDatabaseHas('settings', ['key' => 'late_tolerance_minutes', 'value' => '15']);
        $this->assertDatabaseHas('settings', ['key' => 'overtime_tolerance_minutes', 'value' => '30']);

        // Verify Users & Role associations
        $admin = User::where('email', 'admin@test.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('super_admin'));
        $this->assertTrue((bool) $admin->can_approve_leaves);

        $finance = User::where('email', 'finance@test.com')->first();
        $this->assertNotNull($finance);
        $this->assertTrue($finance->hasRole('finance'));
        $this->assertTrue($finance->hasRole('karyawan'));

        // Verify Devices
        $this->assertDatabaseHas('devices', ['device_uid' => 'ESP8266-LOBBY-01']);
        $this->assertDatabaseHas('devices', ['device_uid' => 'ESP8266-GATE-02']);
    }
}
