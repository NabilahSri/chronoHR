<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::firstOrCreate(['key' => 'late_tolerance_minutes'], ['value' => '15']);
        Setting::firstOrCreate(['key' => 'overtime_tolerance_minutes'], ['value' => '30']);
        Setting::firstOrCreate(['key' => 'wfh_deduction_kartap_pct'], ['value' => '10']);
        Setting::firstOrCreate(['key' => 'wfh_deduction_pkwt_pct'], ['value' => '30']);
        Setting::firstOrCreate(['key' => 'payday_date'], ['value' => '25']);
        Setting::firstOrCreate(['key' => 'office_name'], ['value' => 'Kantor Pusat ChronoHR']);
        Setting::firstOrCreate(['key' => 'office_address'], ['value' => 'Sudirman Central Business District (SCBD) Lot 28, Senayan, Jakarta Selatan']);
        Setting::firstOrCreate(['key' => 'office_latitude'], ['value' => '-6.20880000']);
        Setting::firstOrCreate(['key' => 'office_longitude'], ['value' => '106.84560000']);
        Setting::firstOrCreate(['key' => 'office_radius_meters'], ['value' => '100']);
    }
}

