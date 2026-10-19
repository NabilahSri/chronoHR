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
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'overtime_tolerance_minutes' => 'required|integer|min:0|max:180',
            'wfh_deduction_kartap_pct' => 'required|numeric|min:0|max:100',
            'wfh_deduction_pkwt_pct' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        return back()->with('success', 'Pengaturan parameter toleransi dan potongan presensi berhasil disimpan.');
    }
}
