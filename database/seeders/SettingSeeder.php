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
    }
}
