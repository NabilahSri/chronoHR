<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Device::create([
            'name' => 'NodeMCU Scanner Lobby',
            'device_uid' => 'ESP8266-LOBBY-01',
            'ip_address' => '192.168.1.105',
            'location' => 'Lobby Gedung Utama (Pintu Masuk)',
            'status' => 'ONLINE',
            'mode' => 'ATTENDANCE',
            'firmware_version' => '2.1.0',
            'last_ping_at' => now(),
            'notes' => 'Perangkat utama presensi tap RFID card RC522 pintu masuk',
        ]);

        Device::create([
            'name' => 'NodeMCU Scanner Pintu Belakang',
            'device_uid' => 'ESP8266-GATE-02',
            'ip_address' => '192.168.1.106',
            'location' => 'Pintu Belakang / Parkir Karyawan',
            'status' => 'MAINTENANCE',
            'mode' => 'MAINTENANCE',
            'firmware_version' => '2.0.4',
            'last_ping_at' => now()->subMinutes(12),
            'notes' => 'Sedang dalam kalibrasi antena RFID dan pengecekan sinyal Wi-Fi',
        ]);

        Device::create([
            'name' => 'NodeMCU Scanner Lantai 2 (Engineering)',
            'device_uid' => 'ESP8266-ENG-03',
            'ip_address' => '192.168.1.107',
            'location' => 'Akses Sayap Barat Lantai 2',
            'status' => 'ONLINE',
            'mode' => 'ATTENDANCE',
            'firmware_version' => '2.1.0',
            'last_ping_at' => now()->subMinutes(2),
            'notes' => 'Scanner kehadiran divisi R&D dan Technology',
        ]);

        Device::create([
            'name' => 'NodeMCU Scanner Ruang Server',
            'device_uid' => 'ESP8266-SRV-04',
            'ip_address' => '192.168.1.108',
            'location' => 'Data Center & IT Operations',
            'status' => 'ONLINE',
            'mode' => 'REGISTRATION',
            'firmware_version' => '2.1.2',
            'last_ping_at' => now()->subMinutes(1),
            'notes' => 'Mode pendaftaran kartu RFID baru & akses terbatas',
        ]);
    }
}
