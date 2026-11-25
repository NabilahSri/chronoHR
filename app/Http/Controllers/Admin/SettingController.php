<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $holidayYear = (int) $request->input('holiday_year', now()->year);

        $holidays = Holiday::whereYear('date', $holidayYear)
            ->orderBy('date', 'asc')
            ->paginate(10, ['*'], 'holiday_page')
            ->withQueryString();

        return view('admin.settings.index', [
            'appName' => !empty($settings['app_name']) ? $settings['app_name'] : 'ChronoHR',
            'appSlogan' => !empty($settings['app_slogan']) ? $settings['app_slogan'] : 'Presisi Waktu & Penggajian',
            'appLogoPath' => !empty($settings['app_logo_path']) ? $settings['app_logo_path'] : null,
            'workStartTime' => !empty($settings['work_start_time']) ? $settings['work_start_time'] : '08:00',
            'workEndTime' => !empty($settings['work_end_time']) ? $settings['work_end_time'] : '17:00',
            'lateTolerance' => $settings['late_tolerance_minutes'] ?? '15',
            'overtimeTolerance' => $settings['overtime_tolerance_minutes'] ?? '30',
            'wfaDeductionKartap' => $settings['wfa_deduction_kartap_pct'] ?? '10',
            'wfaDeductionPkwt' => $settings['wfa_deduction_pkwt_pct'] ?? '30',
            'paydayDate' => $settings['payday_date'] ?? '25',
            'officeName' => $settings['office_name'] ?? 'Kantor Pusat ChronoHR',
            'officeAddress' => $settings['office_address'] ?? 'Sudirman Central Business District (SCBD) Lot 28, Senayan, Jakarta Selatan',
            'officeLatitude' => $settings['office_latitude'] ?? '-6.20880000',
            'officeLongitude' => $settings['office_longitude'] ?? '106.84560000',
            'officeRadiusMeters' => $settings['office_radius_meters'] ?? '100',
            'holidays' => $holidays,
            'holidayYear' => $holidayYear,
        ]);
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'type' => 'required|in:NASIONAL,CUTI_BERSAMA,INTERNAL',
        ]);

        Holiday::updateOrCreate(
            ['date' => $validated['date']],
            [
                'name' => $validated['name'],
                'type' => $validated['type'],
            ]
        );

        return back()->with('success', "Hari libur '{$validated['name']}' pada tanggal {$validated['date']} berhasil disimpan ke kalender kerja.");
    }

    public function destroyHoliday(Holiday $holiday): RedirectResponse
    {
        $name = $holiday->name;
        $holiday->delete();

        return back()->with('success', "Hari libur '{$name}' berhasil dihapus dari kalender kerja.");
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:60',
            'app_slogan' => 'nullable|string|max:120',
            'app_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'reset_app_logo' => 'nullable|boolean',
            'work_start_time' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'work_end_time' => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'overtime_tolerance_minutes' => 'required|integer|min:0|max:180',
            'wfa_deduction_kartap_pct' => 'required|numeric|min:0|max:100',
            'wfa_deduction_pkwt_pct' => 'required|numeric|min:0|max:100',
            'payday_date' => 'required|integer|min:1|max:31',
            'office_name' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:500',
            'office_latitude' => 'nullable|numeric|between:-90,90',
            'office_longitude' => 'nullable|numeric|between:-180,180',
            'office_radius_meters' => 'nullable|integer|min:10|max:50000',
        ]);

        $excludedKeys = ['app_logo', 'reset_app_logo'];

        foreach ($validated as $key => $value) {
            if (!in_array($key, $excludedKeys, true) && $value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
            }
        }

        if (!empty($validated['reset_app_logo'])) {
            Setting::updateOrCreate(['key' => 'app_logo_path'], ['value' => '']);
        }

        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $destinationDir = public_path('uploads/branding');
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = 'logo_' . time() . '_' . substr(md5(uniqid('', true)), 0, 6) . '.' . $ext;
            $file->move($destinationDir, $filename);

            Setting::updateOrCreate(['key' => 'app_logo_path'], ['value' => 'uploads/branding/' . $filename]);
        }

        Setting::clearBrandCache();

        return back()->with('success', 'Pengaturan identitas aplikasi, parameter toleransi, potongan presensi, tanggal gajian, dan titik lokasi kantor berhasil disimpan.');
    }
}
