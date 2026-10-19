<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeviceManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_super_admin_can_view_devices_and_status(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('admin.devices.index'));

        $response->assertOk();
        $response->assertSee('Total Alat Terpasang');
    }

    public function test_super_admin_can_register_new_device(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->post(route('admin.devices.store'), [
            'name' => 'Scanner Test Gate',
            'device_uid' => 'ESP-TEST-99',
            'location' => 'Gate Timur',
            'firmware_version' => '1.0.0',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('devices', [
            'device_uid' => 'ESP-TEST-99',
            'name' => 'Scanner Test Gate',
        ]);
    }

    public function test_super_admin_can_toggle_device_maintenance_mode(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $device = Device::create([
            'name' => 'Scanner Maintenance Check',
            'device_uid' => 'ESP-MAINT-01',
            'location' => 'Lobby',
            'status' => 'ONLINE',
            'mode' => 'ATTENDANCE',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.devices.maintenance', $device))
            ->assertRedirect();

        $device->refresh();
        $this->assertEquals('MAINTENANCE', $device->status);
        $this->assertEquals('MAINTENANCE', $device->mode);

        // Toggle back
        $this->actingAs($admin)
            ->patch(route('admin.devices.maintenance', $device))
            ->assertRedirect();

        $device->refresh();
        $this->assertEquals('ONLINE', $device->status);
        $this->assertEquals('ATTENDANCE', $device->mode);
    }

    public function test_super_admin_can_set_device_mode(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $device = Device::create([
            'name' => 'Scanner Mode Check',
            'device_uid' => 'ESP-MODE-01',
            'location' => 'Lobby',
            'status' => 'ONLINE',
            'mode' => 'ATTENDANCE',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.devices.mode', $device), [
                'mode' => 'REGISTRATION',
            ])
            ->assertRedirect();

        $device->refresh();
        $this->assertEquals('REGISTRATION', $device->mode);
    }

    public function test_nodemcu_hardware_can_send_heartbeat(): void
    {
        $device = Device::create([
            'name' => 'NodeMCU Hardware',
            'device_uid' => 'ESP-HARDWARE-01',
            'location' => 'Front Door',
            'status' => 'ONLINE',
            'mode' => 'ATTENDANCE',
        ]);

        $response = $this->postJson(route('devices.heartbeat'), [
            'device_uid' => 'ESP-HARDWARE-01',
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'OK',
                'device_uid' => 'ESP-HARDWARE-01',
                'mode' => 'ATTENDANCE',
            ]);

        $device->refresh();
        $this->assertNotNull($device->last_ping_at);
    }

    public function test_non_admin_cannot_access_device_management(): void
    {
        $employee = User::where('email', 'karyawan@test.com')->first();

        $this->actingAs($employee)
            ->get(route('admin.devices.index'))
            ->assertForbidden();
    }
}
