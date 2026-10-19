<!-- MODAL RINCIAN LENGKAP SLIP GAJI -->
<div id="slipDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden animate-slide-in-up">
        
        <!-- Modal Top Banner -->
        <div class="p-6 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-500/20 text-sky-400 border border-sky-400/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-white tracking-tight">Rincian Resmi Slip Gaji Personil</h3>
                        <span id="modal_slip_status_badge" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            PUBLISHED
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5" id="modal_slip_doc_number">No. Dokumen: CHR-PAY/202610/XXXX</p>
                </div>
            </div>
            <button type="button" onclick="closeSlipModal()"
                class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
            
            <!-- Employee Info Header -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Personil Karyawan</span>
                    <div class="text-sm font-bold text-slate-900 mt-0.5" id="modal_slip_user_name">—</div>
                    <div class="text-xs text-slate-500 font-mono" id="modal_slip_user_email">—</div>
                    <div class="mt-1 flex items-center gap-1.5">
                        <span id="modal_slip_employment_badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                            KARTAP
                        </span>
                        <span class="text-[11px] text-slate-400" id="modal_slip_bank_account">—</span>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Periode & Ketetapan</span>
                    <div class="text-sm font-bold text-slate-900 mt-0.5" id="modal_slip_period">—</div>
                    <div class="text-xs text-slate-500" id="modal_slip_payday_info">Jadwal Gajian: Tanggal 25 setiap bulan</div>
                    <div class="text-xs text-slate-400 mt-1 italic font-sans" id="modal_slip_daily_rate">Tarif Harian: —</div>
                </div>
            </div>

            <!-- Attendance Stats Grid -->
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Rekapitulasi Kehadiran, Izin & Lembur</span>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-center">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-xs sm:text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_working_days">0 hr</div>
                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tight mt-0.5">Hari Kerja</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200">
                        <div class="text-xs sm:text-sm font-extrabold text-emerald-700 font-mono" id="modal_slip_onsite_days">0 hr</div>
                        <div class="text-[9px] font-bold text-emerald-600 uppercase tracking-tight mt-0.5">Hadir Kantor</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-sky-50 border border-sky-200">
                        <div class="text-xs sm:text-sm font-extrabold text-sky-700 font-mono" id="modal_slip_wfh_days">0 hr</div>
                        <div class="text-[9px] font-bold text-sky-600 uppercase tracking-tight mt-0.5">Kerja WFH</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-200">
                        <div class="text-xs sm:text-sm font-extrabold text-indigo-700 font-mono" id="modal_slip_permit_days">0 hr</div>
                        <div class="text-[9px] font-bold text-indigo-600 uppercase tracking-tight mt-0.5">Izin (Unpaid)</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200">
                        <div class="text-xs sm:text-sm font-extrabold text-rose-700 font-mono" id="modal_slip_sick_days">0 hr</div>
                        <div class="text-[9px] font-bold text-rose-600 uppercase tracking-tight mt-0.5">Sakit (Unpaid)</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200">
                        <div class="text-xs sm:text-sm font-extrabold text-amber-700 font-mono" id="modal_slip_overtime_hours">0 jam</div>
                        <div class="text-[9px] font-bold text-amber-600 uppercase tracking-tight mt-0.5">Lembur</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown: Earnings vs Deductions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Earnings Card -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-900 text-white px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>1. Penerimaan (Earnings)</span>
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="p-3.5 space-y-2.5 text-xs bg-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800" id="modal_slip_basic_salary_label">Gaji Pokok</div>
                                <div class="text-[10px] text-slate-400">Gaji acuan bulanan</div>
                            </div>
                            <span class="font-mono font-bold text-slate-900" id="modal_slip_basic_salary">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800" id="modal_slip_overtime_label">Upah Lembur Resmi</div>
                                <div class="text-[10px] text-slate-400" id="modal_slip_overtime_sub">Tarif Rp21.000 / jam</div>
                            </div>
                            <span class="font-mono font-bold text-emerald-600" id="modal_slip_overtime_pay">+Rp 0</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between font-bold">
                            <span class="text-slate-900">Total Kotor (Gross)</span>
                            <span class="font-mono text-emerald-700" id="modal_slip_gross_salary">Rp 0</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions Card -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-800 text-white px-3.5 py-2.5 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                        <span>2. Potongan (Deductions)</span>
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                    <div class="p-3.5 space-y-2.5 text-xs bg-white">
                        <!-- Potongan WFH -->
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800">Potongan Remote (WFH)</div>
                                <div class="text-[10px] text-slate-400" id="modal_slip_wfh_deduction_sub">KARTAP 10% / PKWT 30%</div>
                            </div>
                            <span class="font-mono font-bold text-rose-600" id="modal_slip_wfh_deduction">-Rp 0</span>
                        </div>
                        <!-- Potongan Izin -->
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800">Potongan Izin Tidak Masuk</div>
                                <div class="text-[10px] text-slate-400" id="modal_slip_permit_deduction_sub">100% full tarif harian (unpaid)</div>
                            </div>
                            <span class="font-mono font-bold text-rose-600" id="modal_slip_permit_deduction">-Rp 0</span>
                        </div>
                        <!-- Potongan Sakit -->
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-800">Potongan Sakit (Unpaid)</div>
                                <div class="text-[10px] text-slate-400" id="modal_slip_sick_deduction_sub">100% full tarif harian (unpaid)</div>
                            </div>
                            <span class="font-mono font-bold text-rose-600" id="modal_slip_sick_deduction">-Rp 0</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between font-bold">
                            <span class="text-slate-900">Total Pemotongan Gaji</span>
                            <span class="font-mono text-rose-600" id="modal_slip_total_deduction">-Rp 0</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Take Home Pay (Gaji Bersih Netto) -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-sky-50 via-indigo-50/40 to-slate-50 border-2 border-sky-300 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-extrabold text-sky-800 uppercase tracking-wider block">TOTAL TAKE HOME PAY (GAJI BERSIH)</span>
                        <p class="text-xs text-slate-500 mt-0.5">Penghasilan netto yang ditransfer ke rekening personil.</p>
                    </div>
                    <div class="text-2xl font-extrabold text-sky-950 font-mono" id="modal_slip_net_salary">
                        Rp 0
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-sky-200/80 text-xs italic text-sky-800">
                    Terbilang: <strong id="modal_slip_terbilang" class="font-semibold font-sans">—</strong>
                </div>
            </div>

        </div>

        <!-- Modal Footer Actions -->
        <div class="p-5 border-t border-slate-100 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3">
            <button type="button" onclick="closeSlipModal()"
                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200 transition cursor-pointer">
                Tutup
            </button>

            <div class="flex items-center gap-2">
                <!-- Preview in Tab -->
                <a id="modal_slip_preview_btn" href="#" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold shadow-2xs hover:shadow transition cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Pratinjau PDF</span>
                </a>

                <!-- Download PDF Button -->
                <a id="modal_slip_download_btn" href="#"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download Slip PDF</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    function openSlipModal(data) {
        document.getElementById('modal_slip_user_name').textContent = data.userName;
        document.getElementById('modal_slip_user_email').textContent = data.userEmail;
        document.getElementById('modal_slip_period').textContent = data.period;
        document.getElementById('modal_slip_doc_number').textContent = 'No. Dokumen: ' + data.docNumber;

        // Employment Badge
        const empBadge = document.getElementById('modal_slip_employment_badge');
        empBadge.textContent = data.employmentType;
        if (data.employmentType === 'INTERN') {
            empBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-300';
            document.getElementById('modal_slip_basic_salary_label').textContent = 'Uang Saku Magang';
            document.getElementById('modal_slip_overtime_label').textContent = 'Insentif Proyek Magang';
            document.getElementById('modal_slip_overtime_sub').textContent = 'Apresiasi kontribusi proyek';
            document.getElementById('modal_slip_wfh_deduction_sub').textContent = 'Bebas potongan operasional WFH';
            document.getElementById('modal_slip_permit_deduction_sub').textContent = 'Bebas potongan izin (Magang)';
            document.getElementById('modal_slip_sick_deduction_sub').textContent = 'Bebas potongan sakit (Magang)';
        } else {
            empBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800';
            document.getElementById('modal_slip_basic_salary_label').textContent = 'Gaji Pokok';
            document.getElementById('modal_slip_overtime_label').textContent = 'Upah Lembur Resmi';
            document.getElementById('modal_slip_overtime_sub').textContent = data.overtimeHours + ' jam @ Rp21.000 / jam';
            document.getElementById('modal_slip_wfh_deduction_sub').textContent = (data.employmentType === 'PKWT' ? 'PKWT 30%' : 'KARTAP 10%') + ' dari tarif harian (' + data.wfhDays + ' hr)';
            document.getElementById('modal_slip_permit_deduction_sub').textContent = (data.permitDays || 0) + ' hari Izin @ 100% full (unpaid)';
            document.getElementById('modal_slip_sick_deduction_sub').textContent = (data.sickDays || 0) + ' hari Sakit @ 100% full (unpaid)';
        }

        // Bank Account
        document.getElementById('modal_slip_bank_account').textContent = data.bankAccount;
        document.getElementById('modal_slip_daily_rate').textContent = 'Tarif Harian: ' + data.dailyRate;

        // Status Badge
        const statusBadge = document.getElementById('modal_slip_status_badge');
        statusBadge.textContent = data.status;
        if (data.status === 'PUBLISHED') {
            statusBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
        } else {
            statusBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30';
        }

        // Stats
        document.getElementById('modal_slip_working_days').textContent = data.workingDays + ' hr';
        document.getElementById('modal_slip_onsite_days').textContent = data.onsiteDays + ' hr';
        document.getElementById('modal_slip_wfh_days').textContent = data.wfhDays + ' hr';
        document.getElementById('modal_slip_permit_days').textContent = (data.permitDays || 0) + ' hr';
        document.getElementById('modal_slip_sick_days').textContent = (data.sickDays || 0) + ' hr';
        document.getElementById('modal_slip_overtime_hours').textContent = data.overtimeHours + ' jam';

        // Breakdown Amounts
        document.getElementById('modal_slip_basic_salary').textContent = data.basicSalary;
        document.getElementById('modal_slip_overtime_pay').textContent = data.overtimePay;
        document.getElementById('modal_slip_gross_salary').textContent = data.grossSalary;
        
        // Deductions values
        document.getElementById('modal_slip_wfh_deduction').textContent = (data.wfhDeduction && data.wfhDeduction !== 'Rp 0') ? ('-' + data.wfhDeduction) : 'Rp 0';
        document.getElementById('modal_slip_permit_deduction').textContent = (data.permitDeduction && data.permitDeduction !== 'Rp 0') ? ('-' + data.permitDeduction) : 'Rp 0';
        document.getElementById('modal_slip_sick_deduction').textContent = (data.sickDeduction && data.sickDeduction !== 'Rp 0') ? ('-' + data.sickDeduction) : 'Rp 0';
        document.getElementById('modal_slip_total_deduction').textContent = (data.totalDeduction && data.totalDeduction !== 'Rp 0') ? ('-' + data.totalDeduction) : '-Rp 0';
        
        document.getElementById('modal_slip_net_salary').textContent = data.netSalary;
        document.getElementById('modal_slip_terbilang').textContent = data.terbilang;

        // PDF Links
        document.getElementById('modal_slip_preview_btn').href = data.pdfUrl + '?preview=1';
        document.getElementById('modal_slip_download_btn').href = data.pdfUrl;

        document.getElementById('slipDetailModal').classList.remove('hidden');
    }

    function closeSlipModal() {
        document.getElementById('slipDetailModal').classList.add('hidden');
    }
</script>
