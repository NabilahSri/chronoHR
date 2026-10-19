<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', [
            'lateTolerance' => $settings['late_tolerance_minutes'] ?? '15',
            'overtimeTolerance' => $settings['overtime_tolerance_minutes'] ?? '30',
            'wfhDeductionKartap' => $settings['wfh_deduction_kartap_pct'] ?? '10',
            'wfhDeductionPkwt' => $settings['wfh_deduction_pkwt_pct'] ?? '30',
            'paydayDate' => $settings['payday_date'] ?? '25',
            'officeName' => $settings['office_name'] ?? 'Kantor Pusat ChronoHR',
            'officeAddress' => $settings['office_address'] ?? 'Sudirman Central Business District (SCBD) Lot 28, Senayan, Jakarta Selatan',
            'officeLatitude' => $settings['office_latitude'] ?? '-6.20880000',
            'officeLongitude' => $settings['office_longitude'] ?? '106.84560000',
            'officeRadiusMeters' => $settings['office_radius_meters'] ?? '100',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'overtime_tolerance_minutes' => 'required|integer|min:0|max:180',
            'wfh_deduction_kartap_pct' => 'required|numeric|min:0|max:100',
            'wfh_deduction_pkwt_pct' => 'required|numeric|min:0|max:100',
            'payday_date' => 'required|integer|min:1|max:31',
            'office_name' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:500',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
            'office_radius_meters' => 'nullable|integer|min:10|max:50000',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
            }
        }

        return back()->with('success', 'Pengaturan parameter toleransi, potongan presensi, tanggal gajian, dan titik acuan lokasi kantor berhasil disimpan.');
    }
}
