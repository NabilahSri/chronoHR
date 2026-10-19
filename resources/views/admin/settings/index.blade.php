@extends('layouts.app')

@section('title', 'Toleransi & Parameter Sistem — ChronoHR ')
@section('page_title', 'Parameter Presensi & Otomasi Lembur')

@section('content')
    <div class="w-full">
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs backdrop-blur-xs">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 tracking-tight">Aturan Toleransi Server Chronometer
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi batas keterlambatan pagi, ambang lembur, dan kebijakan pemotongan WFH.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Parameter Toleransi Masuk & Lembur Pulang (2 Kolom Lebar Penuh) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Parameter Late Tolerance -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                    Toleransi Keterlambatan Masuk Kantor
                                </label>
                                <span
                                    class="font-mono text-[11px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-200 shrink-0">
                                    late_tolerance_minutes
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Batas waktu check-in pagi sebelum ditandai status terlambat.</p>
                        </div>

                        <div class="relative w-full">
                            <input type="number" name="late_tolerance_minutes"
                                value="{{ old('late_tolerance_minutes', $lateTolerance) }}" min="0" max="120"
                                required
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                            <span
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit</span>
                        </div>

                        <div
                            class="p-3 bg-white rounded-xl border border-slate-200/80 text-[11px] text-slate-500 flex items-start gap-2">
                            <span class="text-sky-600 font-bold shrink-0">ℹ️</span>
                            <span>Batas Tap Masuk = <strong>08:00</strong> + <strong>{{ $lateTolerance }} menit</strong>.
                                Karyawan yang tap setelah jam tersebut otomatis tercatat <code>is_late = true</code>.</span>
                        </div>
                    </div>

                    <!-- Parameter Overtime Tolerance -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                    Toleransi Ambang Lembur Pulang (Checkout)
                                </label>
                                <span
                                    class="font-mono text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 shrink-0">
                                    overtime_tolerance_minutes
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Durasi setelah jam pulang resmi (17:00) sebelum lembur mulai dihitung.</p>
                        </div>

                        <div class="relative w-full">
                            <input type="number" name="overtime_tolerance_minutes"
                                value="{{ old('overtime_tolerance_minutes', $overtimeTolerance) }}" min="0"
                                max="180" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                            <span
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">Menit</span>
                        </div>

                        <div
                            class="p-3 bg-white rounded-xl border border-slate-200/80 text-[11px] text-slate-500 flex items-start gap-2">
                            <span class="text-amber-600 font-bold shrink-0">ℹ️</span>
                            <span>Batas Lembur = <strong>17:00</strong> + <strong>{{ $overtimeTolerance }} menit</strong>. Jika
                                lewat batas ini, jam lembur dihitung <code>Floor(Jam Pulang - 17:00)</code> (Rp21.000/jam).</span>
                        </div>
                    </div>
                </div>

                <!-- Section Divider: Kebijakan Potongan WFH -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Kebijakan Potongan Gaji WFH (Remote)</h3>
                            <p class="text-xs text-slate-500">Persentase potongan dari tarif gaji harian per hari WFH sesuai status ikatan kerja personil.</p>
                        </div>
                    </div>
                </div>

                <!-- Parameter Potongan WFH KARTAP & PKWT (2 Kolom Lebar Penuh) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Parameter Potongan WFH KARTAP -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                    Potongan WFH Karyawan Tetap (KARTAP)
                                </label>
                                <span class="font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 shrink-0">
                                    wfh_deduction_kartap_pct
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Persentase potongan dari tarif harian untuk personil Karyawan Tetap.</p>
                        </div>

                        <div class="relative w-full">
                            <input type="number" name="wfh_deduction_kartap_pct"
                                value="{{ old('wfh_deduction_kartap_pct', $wfhDeductionKartap) }}" min="0" max="100" step="0.5" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">%</span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-[11px] text-slate-500 flex items-start gap-2">
                            <span class="text-emerald-600 font-bold shrink-0">ℹ️</span>
                            <span>Rumus: <strong>Hari WFH x (Tarif Harian x {{ $wfhDeductionKartap }}%)</strong>. Mengurangi total gaji kotor saat penggajian.</span>
                        </div>
                    </div>

                    <!-- Parameter Potongan WFH PKWT -->
                    <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                                    Potongan WFH Karyawan Kontrak (PKWT)
                                </label>
                                <span class="font-mono text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 shrink-0">
                                    wfh_deduction_pkwt_pct
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Persentase potongan dari tarif harian untuk personil Kontrak Kerja (PKWT).</p>
                        </div>

                        <div class="relative w-full">
                            <input type="number" name="wfh_deduction_pkwt_pct"
                                value="{{ old('wfh_deduction_pkwt_pct', $wfhDeductionPkwt) }}" min="0" max="100" step="0.5" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono font-bold focus:outline-none focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-semibold text-slate-400 pointer-events-none">%</span>
                        </div>

                        <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-[11px] text-slate-500 flex items-start gap-2">
                            <span class="text-amber-600 font-bold shrink-0">ℹ️</span>
                            <span>Rumus: <strong>Hari WFH x (Tarif Harian x {{ $wfhDeductionPkwt }}%)</strong>. Mengurangi total gaji kotor saat penggajian.</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end pt-5 border-t border-slate-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold shadow-sm hover:shadow active:scale-[0.99] transition-all cursor-pointer">
                        Simpan Perubahan Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
