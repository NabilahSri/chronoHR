<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'Tahun Baru 2026 Masehi', 'type' => 'NASIONAL'],
            ['date' => '2026-01-16', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'type' => 'NASIONAL'],
            ['date' => '2026-02-17', 'name' => 'Tahun Baru Imlek 2577 Kongzili', 'type' => 'NASIONAL'],
            ['date' => '2026-03-19', 'name' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'type' => 'NASIONAL'],
            ['date' => '2026-03-20', 'name' => 'Hari Raya Idul Fitri 1447 H', 'type' => 'NASIONAL'],
            ['date' => '2026-03-23', 'name' => 'Cuti Bersama Idul Fitri 1447 H', 'type' => 'CUTI_BERSAMA'],
            ['date' => '2026-03-24', 'name' => 'Cuti Bersama Idul Fitri 1447 H', 'type' => 'CUTI_BERSAMA'],
            ['date' => '2026-04-03', 'name' => 'Wafat Yesus Kristus', 'type' => 'NASIONAL'],
            ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional', 'type' => 'NASIONAL'],
            ['date' => '2026-05-14', 'name' => 'Kenaikan Yesus Kristus', 'type' => 'NASIONAL'],
            ['date' => '2026-05-27', 'name' => 'Hari Raya Idul Adha 1447 H', 'type' => 'NASIONAL'],
            ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila', 'type' => 'NASIONAL'],
            ['date' => '2026-06-16', 'name' => 'Tahun Baru Islam 1448 H', 'type' => 'NASIONAL'],
            ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan Republik Indonesia', 'type' => 'NASIONAL'],
            ['date' => '2026-08-25', 'name' => 'Maulid Nabi Muhammad SAW', 'type' => 'NASIONAL'],
            ['date' => '2026-12-24', 'name' => 'Cuti Bersama Hari Raya Natal', 'type' => 'CUTI_BERSAMA'],
            ['date' => '2026-12-25', 'name' => 'Hari Raya Natal', 'type' => 'NASIONAL'],
        ];

        foreach ($holidays as $item) {
            Holiday::updateOrCreate(
                ['date' => $item['date']],
                ['name' => $item['name'], 'type' => $item['type']]
            );
        }
    }
}
