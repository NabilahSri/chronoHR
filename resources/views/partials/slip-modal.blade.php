<!-- MODAL RINCIAN & PRATINJAU SLIP GAJI -->
<div id="slipDetailModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal_slip_title"
    onclick="if(event.target === this) closeSlipModal()"
    class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 p-4 sm:p-6 flex items-center justify-center">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-4xl w-full overflow-hidden">

        <!-- Header Modal Sederhana & Bersih -->
        <div class="px-5 py-4 sm:px-6 bg-white border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start sm:items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 id="modal_slip_title" class="text-base font-bold text-slate-900 tracking-tight">Slip Gaji Karyawan</h3>
                        <span id="modal_slip_status_badge"
                            class="px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-300">
                            DITERBITKAN
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-0.5 truncate" id="modal_slip_doc_number">
                        No. Dokumen: CHR-PAY/202610/XXXX
                    </p>
                </div>
            </div>

            <!-- Pengalih Tab & Tombol Tutup -->
            <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200" role="tablist">
                    <button type="button"
                        id="modal_tab_btn_detail"
                        onclick="switchSlipModalTab('detail')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 bg-slate-900 text-white">
                        Rincian Slip
                    </button>
                    <button type="button"
                        id="modal_tab_btn_preview"
                        onclick="switchSlipModalTab('preview')"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 text-slate-600 hover:text-slate-900">
                        Pratinjau PDF
                    </button>
                </div>

                <button type="button"
                    onclick="closeSlipModal()"
                    aria-label="Tutup modal slip gaji"
                    class="w-9 h-9 inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- TAB 1: RINCIAN KOMPONEN SLIP GAJI -->
        <div id="modal_tab_panel_detail" class="p-5 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">

            <!-- Identitas Personil & Ketetapan Periode -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Identitas Personil</span>
                    <div class="text-sm font-bold text-slate-900" id="modal_slip_user_name">-</div>
                    <div class="text-xs text-slate-600 font-mono" id="modal_slip_user_email">-</div>
                    <div class="pt-1 flex items-center gap-2 flex-wrap">
                        <span id="modal_slip_employment_badge"
                            class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-200 text-slate-800">
                            KARTAP
                        </span>
                        <span class="text-xs text-slate-700 font-mono" id="modal_slip_bank_account">-</span>
                    </div>
                </div>

                <div class="space-y-1 sm:border-l sm:border-slate-200 sm:pl-4">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Periode & Ketetapan</span>
                    <div class="text-sm font-bold text-slate-900" id="modal_slip_period">-</div>
                    <div class="text-xs text-slate-700" id="modal_slip_payday_info">Jadwal Gajian: Tanggal 25 setiap bulan</div>
                    <div class="text-xs text-slate-700 font-mono pt-0.5" id="modal_slip_daily_rate">Tarif Harian: -</div>
                </div>
            </div>

            <!-- Rekapitulasi Kehadiran, Izin & Lembur (Sederhana & Seragam) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Rekapitulasi Kehadiran & Jam Kerja</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2.5 text-center">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_working_days">0 hr</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Hari Kerja</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_onsite_days">0 hr</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Hadir Kantor</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_wfa_days">0 hr</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Kerja WFA</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_permit_days">0 hr</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Izin (Tanpa Upah)</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_sick_days">0 hr</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Sakit (Tanpa Upah)</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-sm font-extrabold text-slate-900 font-mono" id="modal_slip_overtime_hours">0 jam</div>
                        <div class="text-[10px] font-bold text-slate-600 uppercase mt-0.5">Lembur Resmi</div>
                    </div>
                </div>
            </div>

            <!-- Komponen Penerimaan vs Pemotongan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- 1. Penerimaan -->
                <div class="rounded-xl border border-slate-200 overflow-hidden flex flex-col justify-between bg-white">
                    <div>
                        <div class="bg-slate-100 text-slate-900 border-b border-slate-200 px-4 py-2.5 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                            <span>1. Rincian Penerimaan</span>
                            <span class="text-[10px] font-mono text-slate-600">JUMLAH (RP)</span>
                        </div>
                        <div class="p-4 space-y-3 text-xs">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-bold text-slate-900" id="modal_slip_basic_salary_label">Gaji Pokok Bulanan</div>
                                    <div class="text-[11px] text-slate-600" id="modal_slip_basic_salary_sub">Gaji pokok sesuai kontrak kerja</div>
                                </div>
                                <span class="font-mono font-bold text-slate-900 shrink-0" id="modal_slip_basic_salary">Rp 0</span>
                            </div>
                            <div class="flex items-start justify-between gap-2 pt-2 border-t border-slate-100">
                                <div>
                                    <div class="font-bold text-slate-900" id="modal_slip_overtime_label">Upah Lembur Resmi</div>
                                    <div class="text-[11px] text-slate-600" id="modal_slip_overtime_sub">Tarif Rp21.000 / jam</div>
                                </div>
                                <span class="font-mono font-bold text-slate-900 shrink-0" id="modal_slip_overtime_pay">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-900">Total Penerimaan Kotor</span>
                        <span class="font-mono text-slate-900 text-sm" id="modal_slip_gross_salary">Rp 0</span>
                    </div>
                </div>

                <!-- 2. Pemotongan -->
                <div class="rounded-xl border border-slate-200 overflow-hidden flex flex-col justify-between bg-white">
                    <div>
                        <div class="bg-slate-100 text-slate-900 border-b border-slate-200 px-4 py-2.5 text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                            <span>2. Rincian Pemotongan</span>
                            <span class="text-[10px] font-mono text-slate-600">JUMLAH (RP)</span>
                        </div>
                        <div class="p-4 space-y-2.5 text-xs">
                            <!-- Potongan WFA -->
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-bold text-slate-900">Potongan Kehadiran WFA</div>
                                    <div class="text-[11px] text-slate-600" id="modal_slip_wfa_deduction_sub">Sesuai kebijakan ikatan kerja</div>
                                </div>
                                <span class="font-mono font-bold text-slate-900 shrink-0" id="modal_slip_wfa_deduction">Rp 0</span>
                            </div>
                            <!-- Potongan Izin -->
                            <div class="flex items-start justify-between gap-2 pt-2 border-t border-slate-100">
                                <div>
                                    <div class="font-bold text-slate-900">Potongan Izin (Tanpa Upah)</div>
                                    <div class="text-[11px] text-slate-600" id="modal_slip_permit_deduction_sub">100% tarif harian per hari izin</div>
                                </div>
                                <span class="font-mono font-bold text-slate-900 shrink-0" id="modal_slip_permit_deduction">Rp 0</span>
                            </div>
                            <!-- Potongan Sakit -->
                            <div class="flex items-start justify-between gap-2 pt-2 border-t border-slate-100">
                                <div>
                                    <div class="font-bold text-slate-900">Potongan Sakit (Tanpa Upah)</div>
                                    <div class="text-[11px] text-slate-600" id="modal_slip_sick_deduction_sub">100% tarif harian per hari sakit</div>
                                </div>
                                <span class="font-mono font-bold text-slate-900 shrink-0" id="modal_slip_sick_deduction">Rp 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-900">Total Pemotongan Gaji</span>
                        <span class="font-mono text-slate-900 text-sm" id="modal_slip_total_deduction">Rp 0</span>
                    </div>
                </div>

            </div>

            <!-- Total Gaji Bersih Diterima -->
            <div class="p-4 rounded-xl bg-slate-50 border-2 border-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wider block">
                            TOTAL GAJI BERSIH DITERIMA
                        </span>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Rumus: Total Penerimaan Kotor dikurangi Total Pemotongan Resmi (WFA & Sakit/Izin).
                        </p>
                    </div>
                    <div class="text-2xl font-extrabold text-slate-900 font-mono shrink-0" id="modal_slip_net_salary">
                        Rp 0
                    </div>
                </div>
                <div class="mt-2.5 pt-2 border-t border-slate-200 text-xs text-slate-800">
                    Terbilang: <strong id="modal_slip_terbilang" class="font-bold italic">-</strong>
                </div>
            </div>

        </div>

        <!-- TAB 2: PRATINJAU DOKUMEN PDF -->
        <div id="modal_tab_panel_preview" class="hidden p-5 sm:p-6 bg-slate-100 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-white px-4 py-3 rounded-xl border border-slate-200 text-xs">
                <div class="flex items-center gap-2 text-slate-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-slate-700"></span>
                    <span id="modal_slip_preview_status">Memuat pratinjau dokumen PDF resmi ukuran A4...</span>
                </div>
                <a id="modal_slip_open_newtab_link" href="#" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-1.5 font-bold text-slate-900 hover:text-slate-700 underline">
                    <span>Buka di Tab Baru Penuh</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            <div class="p-2 sm:p-3 bg-slate-200/70 rounded-xl border border-slate-300">
                <iframe id="modal_slip_pdf_iframe"
                    src="about:blank"
                    title="Pratinjau Dokumen PDF Slip Gaji"
                    onload="if(this.src !== 'about:blank') { document.getElementById('modal_slip_preview_status').textContent = 'Pratinjau dokumen PDF A4 siap ditampilkan.'; }"
                    class="w-full h-[60vh] rounded-lg border border-slate-300 bg-white shadow-xs"></iframe>
            </div>
        </div>

        <!-- Aksi Bawah Modal -->
        <div class="px-5 py-4 sm:px-6 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <button type="button"
                onclick="closeSlipModal()"
                class="min-h-[40px] px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900">
                Tutup
            </button>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Buka Pratinjau PDF di Tab Baru -->
                <a id="modal_slip_preview_btn" href="#" target="_blank" rel="noopener"
                    class="min-h-[40px] inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 rounded-xl text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900">
                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Pratinjau Tab Baru</span>
                </a>

                <!-- Tombol Unduh PDF -->
                <a id="modal_slip_download_btn" href="#"
                    class="min-h-[40px] inline-flex items-center gap-2 px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900">
                    <svg class="w-4 h-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Slip PDF</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    let currentSlipPdfPreviewUrl = '';

    function switchSlipModalTab(tab) {
        const detailPanel = document.getElementById('modal_tab_panel_detail');
        const previewPanel = document.getElementById('modal_tab_panel_preview');
        const detailBtn = document.getElementById('modal_tab_btn_detail');
        const previewBtn = document.getElementById('modal_tab_btn_preview');
        const iframe = document.getElementById('modal_slip_pdf_iframe');

        if (tab === 'preview') {
            detailPanel.classList.add('hidden');
            previewPanel.classList.remove('hidden');
            previewBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 bg-slate-900 text-white';
            detailBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 text-slate-600 hover:text-slate-900';

            if (currentSlipPdfPreviewUrl && iframe.src !== currentSlipPdfPreviewUrl) {
                document.getElementById('modal_slip_preview_status').textContent = 'Memuat pratinjau dokumen PDF resmi ukuran A4...';
                iframe.src = currentSlipPdfPreviewUrl;
            }
        } else {
            previewPanel.classList.add('hidden');
            detailPanel.classList.remove('hidden');
            detailBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 bg-slate-900 text-white';
            previewBtn.className = 'px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-slate-900 text-slate-600 hover:text-slate-900';
        }
    }

    function openSlipModal(data, initialTab = 'detail') {
        document.getElementById('modal_slip_user_name').textContent = data.userName || '-';
        document.getElementById('modal_slip_user_email').textContent = data.userEmail || '-';
        document.getElementById('modal_slip_period').textContent = data.period || '-';
        document.getElementById('modal_slip_doc_number').textContent = 'No. Dokumen: ' + (data.docNumber || '-');

        const paydayDate = data.paydayDate || '25';
        const kartapPct = data.wfaKartapPct || '10';
        const pkwtPct = data.wfaPkwtPct || '30';

        document.getElementById('modal_slip_payday_info').textContent = 'Jadwal Gajian: Tanggal ' + paydayDate + ' setiap bulan';

        const empBadge = document.getElementById('modal_slip_employment_badge');
        if (data.employmentType === 'INTERN') {
            empBadge.textContent = 'MAGANG';
            empBadge.className = 'px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-200 text-slate-800';
            document.getElementById('modal_slip_basic_salary_label').textContent = 'Uang Saku Magang';
            document.getElementById('modal_slip_basic_salary_sub').textContent = 'Uang saku sukarela / ketetapan magang';
            document.getElementById('modal_slip_overtime_label').textContent = 'Insentif Proyek Magang';
            document.getElementById('modal_slip_overtime_sub').textContent = 'Apresiasi kontribusi proyek berjalan';
            document.getElementById('modal_slip_wfa_deduction_sub').textContent = 'Bebas potongan operasional WFA (Magang)';
            document.getElementById('modal_slip_permit_deduction_sub').textContent = 'Bebas potongan izin (Magang)';
            document.getElementById('modal_slip_sick_deduction_sub').textContent = 'Bebas potongan sakit (Magang)';
        } else {
            empBadge.textContent = data.employmentType || 'KARTAP';
            empBadge.className = 'px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-200 text-slate-800';
            const activeWfaPct = data.employmentType === 'PKWT' ? ('PKWT ' + pkwtPct + '%') : ('KARTAP ' + kartapPct + '%');
            document.getElementById('modal_slip_basic_salary_label').textContent = 'Gaji Pokok Bulanan';
            document.getElementById('modal_slip_basic_salary_sub').textContent = 'Gaji pokok sesuai ikatan kerja (' + data.workingDays + ' hari kerja)';
            document.getElementById('modal_slip_overtime_label').textContent = 'Upah Lembur Resmi';
            document.getElementById('modal_slip_overtime_sub').textContent = (data.overtimeHours || 0) + ' jam @ Rp21.000 / jam';
            document.getElementById('modal_slip_wfa_deduction_sub').textContent = (data.wfaDays || 0) + ' hari WFA @ ' + activeWfaPct + ' dari tarif harian';
            document.getElementById('modal_slip_permit_deduction_sub').textContent = (data.permitDays || 0) + ' hari Izin @ 100% tarif harian (tanpa upah)';
            document.getElementById('modal_slip_sick_deduction_sub').textContent = (data.sickDays || 0) + ' hari Sakit @ 100% tarif harian (tanpa upah)';
        }

        document.getElementById('modal_slip_bank_account').textContent = data.bankAccount || '-';
        document.getElementById('modal_slip_daily_rate').textContent = 'Tarif Harian: ' + (data.dailyRate || '-');

        const statusBadge = document.getElementById('modal_slip_status_badge');
        if (data.status === 'PUBLISHED') {
            statusBadge.textContent = 'DITERBITKAN / RESMI';
            statusBadge.className = 'px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-300';
        } else {
            statusBadge.textContent = 'KONSEP INTERNAL';
            statusBadge.className = 'px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-300';
        }

        document.getElementById('modal_slip_working_days').textContent = (data.workingDays || 0) + ' hr';
        document.getElementById('modal_slip_onsite_days').textContent = (data.onsiteDays || 0) + ' hr';
        document.getElementById('modal_slip_wfa_days').textContent = (data.wfaDays || 0) + ' hr';
        document.getElementById('modal_slip_permit_days').textContent = (data.permitDays || 0) + ' hr';
        document.getElementById('modal_slip_sick_days').textContent = (data.sickDays || 0) + ' hr';
        document.getElementById('modal_slip_overtime_hours').textContent = (data.overtimeHours || 0) + ' jam';

        document.getElementById('modal_slip_basic_salary').textContent = data.basicSalary || 'Rp 0';
        document.getElementById('modal_slip_overtime_pay').textContent = (data.overtimePay && data.overtimePay !== 'Rp 0') ? ('+ ' + data.overtimePay) : 'Rp 0';
        document.getElementById('modal_slip_gross_salary').textContent = data.grossSalary || 'Rp 0';

        document.getElementById('modal_slip_wfa_deduction').textContent = (data.wfaDeduction && data.wfaDeduction !== 'Rp 0') ? ('- ' + data.wfaDeduction) : 'Rp 0';
        document.getElementById('modal_slip_permit_deduction').textContent = (data.permitDeduction && data.permitDeduction !== 'Rp 0') ? ('- ' + data.permitDeduction) : 'Rp 0';
        document.getElementById('modal_slip_sick_deduction').textContent = (data.sickDeduction && data.sickDeduction !== 'Rp 0') ? ('- ' + data.sickDeduction) : 'Rp 0';
        document.getElementById('modal_slip_total_deduction').textContent = (data.totalDeduction && data.totalDeduction !== 'Rp 0') ? ('- ' + data.totalDeduction) : 'Rp 0';

        document.getElementById('modal_slip_net_salary').textContent = data.netSalary || 'Rp 0';
        document.getElementById('modal_slip_terbilang').textContent = data.terbilang || '-';

        currentSlipPdfPreviewUrl = data.pdfUrl + '?preview=1';
        document.getElementById('modal_slip_preview_btn').href = currentSlipPdfPreviewUrl;
        document.getElementById('modal_slip_open_newtab_link').href = currentSlipPdfPreviewUrl;
        document.getElementById('modal_slip_download_btn').href = data.pdfUrl;

        const iframe = document.getElementById('modal_slip_pdf_iframe');
        iframe.src = 'about:blank';

        document.getElementById('slipDetailModal').classList.remove('hidden');
        switchSlipModalTab(initialTab);
    }

    function closeSlipModal() {
        const modal = document.getElementById('slipDetailModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        const iframe = document.getElementById('modal_slip_pdf_iframe');
        if (iframe) {
            iframe.src = 'about:blank';
        }
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('slipDetailModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeSlipModal();
            }
        }
    });
</script>
