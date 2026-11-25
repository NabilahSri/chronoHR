<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Slip Gaji - {{ $user->name ?? 'Karyawan' }} ({{ $monthName }} {{ $payroll->year }})</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 34px 40px;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #111827;
            background-color: #ffffff;
            line-height: 1.45;
            margin: 0;
            padding: 12px 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Kop Surat */
        .header-table {
            border-bottom: 1.5px solid #111827;
            margin-bottom: 16px;
        }
        .header-table td {
            padding-bottom: 12px;
            vertical-align: top;
        }
        .company-brand {
            font-size: 17px;
            font-weight: bold;
            color: #111827;
            letter-spacing: 0.2px;
        }
        .company-meta {
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 4px;
            line-height: 1.4;
        }
        .doc-header-right {
            text-align: right;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .doc-status {
            display: inline-block;
            font-size: 8px;
            font-weight: bold;
            color: #374151;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 2px 7px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-number {
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 4px;
            font-family: 'Courier New', Courier, monospace;
        }

        /* Judul Bagian */
        .section-heading {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #374151;
            margin-bottom: 6px;
        }

        /* Tabel Informasi Karyawan */
        .info-box {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            margin-bottom: 16px;
        }
        .info-box td {
            padding: 5px 10px;
            font-size: 9px;
            vertical-align: top;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-box tr:last-child td {
            border-bottom: none;
        }
        .info-label {
            color: #4b5563;
            width: 17%;
        }
        .info-colon {
            width: 2%;
            color: #6b7280;
            text-align: center;
        }
        .info-val {
            color: #111827;
            font-weight: bold;
            width: 31%;
        }

        /* Ringkasan Kehadiran 6 Kolom */
        .attendance-summary {
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            margin-bottom: 16px;
            text-align: center;
        }
        .attendance-summary td {
            padding: 8px 6px;
            border-right: 1px solid #e5e7eb;
            vertical-align: middle;
            width: 16.66%;
        }
        .attendance-summary td:last-child {
            border-right: none;
        }
        .att-num {
            font-size: 10.5px;
            font-weight: bold;
            color: #111827;
            font-family: 'Courier New', Courier, monospace;
        }
        .att-lbl {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.3px;
            margin-top: 3px;
        }

        /* Komponen Penerimaan & Pemotongan */
        .components-wrapper {
            margin-bottom: 16px;
        }
        .components-wrapper > tbody > tr > td {
            vertical-align: top;
            width: 50%;
        }
        .components-wrapper > tbody > tr > td.col-left {
            padding-right: 7px;
        }
        .components-wrapper > tbody > tr > td.col-right {
            padding-left: 7px;
        }

        .box-table {
            border: 1px solid #d1d5db;
        }
        .box-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 10px;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
        }
        .box-table th.amount {
            text-align: right;
        }
        .box-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        .box-table .item-name {
            color: #111827;
            font-weight: bold;
            display: block;
        }
        .box-table .item-sub {
            font-size: 8px;
            color: #6b7280;
            display: block;
            margin-top: 2px;
            line-height: 1.35;
        }
        .box-table .item-amount {
            text-align: right;
            font-weight: bold;
            color: #111827;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            white-space: nowrap;
        }
        .box-table .total-row td {
            background-color: #f9fafb;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #9ca3af;
            border-bottom: none;
            padding: 8px 10px;
        }

        /* Tabel Gaji Bersih (THP) */
        .net-salary-table {
            border: 1.5px solid #111827;
            background-color: #f9fafb;
            margin-bottom: 20px;
        }
        .net-salary-table td.net-main-left {
            padding: 10px 12px 8px 12px;
            vertical-align: middle;
            width: 62%;
        }
        .net-salary-table td.net-main-right {
            padding: 10px 12px 8px 12px;
            vertical-align: middle;
            width: 38%;
            text-align: right;
        }
        .net-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            letter-spacing: 0.4px;
        }
        .net-formula {
            font-size: 8px;
            color: #4b5563;
            margin-top: 3px;
        }
        .net-amount {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            font-family: 'Courier New', Courier, monospace;
            white-space: nowrap;
        }
        .net-terbilang-cell {
            padding: 6px 12px 9px 12px;
            border-top: 1px dashed #d1d5db;
            font-size: 8.5px;
            color: #374151;
        }

        /* Blok Tanda Tangan */
        .signatures-table {
            margin-top: 8px;
            margin-bottom: 18px;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sign-title {
            font-size: 9px;
            color: #374151;
            margin-bottom: 44px;
        }
        .sign-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #374151;
            padding-top: 5px;
            display: inline-block;
            min-width: 170px;
        }
        .sign-role {
            font-size: 8px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Catatan Kaki */
        .footer-note {
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            font-size: 7.5px;
            color: #6b7280;
            text-align: center;
            line-height: 1.45;
        }
    </style>
</head>
<body>
    @php
        $kartapPctVal = $officeSettings['wfa_kartap_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_kartap_pct', 10);
        $pkwtPctVal = $officeSettings['wfa_pkwt_pct'] ?? \App\Models\Setting::getValue('wfa_deduction_pkwt_pct', 30);
        $wfaPct = (($user->employment_type ?? '') === 'PKWT' ? $pkwtPctVal : $kartapPctVal) . '%';
        $permitDeduct = ($payroll->total_permit_days ?? 0) * $payroll->daily_salary_rate;
        $sickDeduct = ($payroll->total_sick_days ?? 0) * $payroll->daily_salary_rate;
        $sanctionDeduct = (float) ($payroll->sanction_deduction_amount ?? 0);
        $totalDeduct = $payroll->wfa_deduction_amount + ($payroll->leave_deduction_amount ?? 0) + $sanctionDeduct;
        $projectIncentiveAmount = isset($projectIncentives)
            ? (float) $projectIncentives
            : (float) \App\Models\ProjectIncentive::where('user_id', $payroll->user_id)
                ->whereMonth('given_date', $payroll->month)
                ->whereYear('given_date', $payroll->year)
                ->sum('amount');
        $docNumber = 'CHR-PAY/' . $payroll->year . sprintf('%02d', $payroll->month) . '/' . strtoupper(substr($payroll->id, 0, 8));
    @endphp

    <!-- Kop Surat & Nomor Dokumen -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 58%;">
                <div class="company-brand">{{ $appBrandName ?? 'ChronoHR' }}</div>
                <div class="company-meta">
                    <strong>{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</strong> · {{ $appBrandSlogan ?? 'Presisi Waktu & Penggajian' }}<br>
                    {{ $officeSettings['address'] ?? 'Gedung Pusat ChronoHR, Jakarta' }}
                </div>
            </td>
            <td style="width: 42%;" class="doc-header-right">
                <div class="doc-title">SLIP GAJI KARYAWAN</div>
                <div class="doc-status">
                    {{ $payroll->status === 'PUBLISHED' ? 'DITERBITKAN / RESMI' : 'KONSEP INTERNAL' }}
                </div>
                <div class="doc-number">
                    No. Dokumen: {{ $docNumber }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Informasi Identitas Personil & Periode -->
    <div class="section-heading">Data Personil & Ketetapan Periode</div>
    <table class="info-box" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-label">Nama Personil</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $user->name ?? '-' }}</td>

            <td class="info-label">Periode Gaji</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $monthName }} {{ $payroll->year }}</td>
        </tr>
        <tr>
            <td class="info-label">Email Karyawan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $user->email ?? '-' }}</td>

            <td class="info-label">Jadwal Gajian</td>
            <td class="info-colon">:</td>
            <td class="info-val">Tanggal {{ $officeSettings['payday'] ?? 25 }} {{ $monthName }} {{ $payroll->year }}</td>
        </tr>
        <tr>
            <td class="info-label">Ikatan Kerja</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                @if(($user->employment_type ?? '') === 'KARTAP')
                    Karyawan Tetap (KARTAP)
                @elseif(($user->employment_type ?? '') === 'PKWT')
                    Kontrak Kerja (PKWT)
                @elseif(($user->employment_type ?? '') === 'INTERN')
                    Program Magang / PKL
                @else
                    {{ $user->employment_type ?? '-' }}
                @endif
            </td>

            <td class="info-label">Tarif Harian</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                @if(($user->employment_type ?? '') === 'INTERN')
                    Skema Uang Saku & Insentif Proyek
                @else
                    Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }} / hari
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Rekening Bank</td>
            <td class="info-colon">:</td>
            <td class="info-val">
                @if(!empty($user->bank_name))
                    {{ $user->bank_name }} - {{ $user->bank_account_number }}
                @else
                    Tunai / Belum Terdaftar
                @endif
            </td>

            <td class="info-label">Tanggal Cetak</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Ringkasan Kehadiran -->
    <div class="section-heading">Rekapitulasi Kehadiran & Jam Kerja</div>
    <table class="attendance-summary" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="att-num">{{ $payroll->total_working_days }} Hari</div>
                <div class="att-lbl">Hari Kerja</div>
            </td>
            <td>
                <div class="att-num">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Fleksibel' : $payroll->total_present_onsite . ' Hari' }}
                </div>
                <div class="att-lbl">Hadir Kantor</div>
            </td>
            <td>
                <div class="att-num">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : $payroll->total_wfa_days . ' Hari' }}
                </div>
                <div class="att-lbl">Kerja WFA</div>
            </td>
            <td>
                <div class="att-num">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : ($payroll->total_permit_days ?? 0) . ' Hari' }}
                </div>
                <div class="att-lbl">Izin (Tanpa Upah)</div>
            </td>
            <td>
                <div class="att-num">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : ($payroll->total_sick_days ?? 0) . ' Hari' }}
                </div>
                <div class="att-lbl">Sakit (Tanpa Upah)</div>
            </td>
            <td>
                <div class="att-num">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? '-' : $payroll->total_overtime_hours . ' Jam' }}
                </div>
                <div class="att-lbl">Lembur Resmi</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Penerimaan & Pemotongan Berdampingan -->
    <div class="section-heading">Rincian Komponen Penerimaan & Pemotongan</div>
    <table class="components-wrapper" cellpadding="0" cellspacing="0">
        <tr>
            <!-- Kolom Kiri: PENERIMAAN -->
            <td class="col-left">
                <table class="box-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>1. Rincian Penerimaan</th>
                            <th class="amount">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(($user->employment_type ?? '') === 'INTERN')
                            <tr>
                                <td>
                                    <span class="item-name">Uang Saku Magang</span>
                                    <span class="item-sub">Nominal sukarela / ketetapan magang bulanan</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($user->basic_salary ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Insentif Proyek Magang</span>
                                    <span class="item-sub">Apresiasi kontribusi penyelesaian proyek</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Tunjangan Tambahan Lainnya</span>
                                    <span class="item-sub">Tidak ada komponen tambahan bulan ini</span>
                                </td>
                                <td class="item-amount">
                                    Rp 0
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <span class="item-name">Gaji Pokok Bulanan</span>
                                    <span class="item-sub">Acuan {{ $payroll->total_working_days }} hari kerja @ Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($user->basic_salary ?? ($payroll->gross_salary - $payroll->overtime_pay), 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Upah Lembur Resmi</span>
                                    <span class="item-sub">{{ $payroll->total_overtime_hours }} jam lembur terverifikasi @ Rp 21.000/jam</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Insentif Proyek Tambahan</span>
                                    <span class="item-sub">
                                        @if($projectIncentiveAmount > 0)
                                            Bonus penyelesaian proyek periode {{ $monthName }} {{ $payroll->year }}
                                        @else
                                            Tidak ada insentif proyek bulan ini
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($projectIncentiveAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Total Penerimaan Kotor</td>
                            <td class="item-amount">
                                Rp {{ number_format($payroll->gross_salary, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <!-- Kolom Kanan: PEMOTONGAN -->
            <td class="col-right">
                <table class="box-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>2. Rincian Pemotongan</th>
                            <th class="amount">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(($user->employment_type ?? '') === 'INTERN')
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Kehadiran Jarak Jauh (WFA)</span>
                                    <span class="item-sub">Bebas pemotongan operasional (Magang)</span>
                                </td>
                                <td class="item-amount">
                                    Rp 0
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Izin Tidak Masuk</span>
                                    <span class="item-sub">Bebas potongan kehadiran magang</span>
                                </td>
                                <td class="item-amount">
                                    Rp 0
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Sakit</span>
                                    <span class="item-sub">Bebas potongan kehadiran magang</span>
                                </td>
                                <td class="item-amount">
                                    Rp 0
                                </td>
                            </tr>
                        @else
                            <!-- Item 1: WFA -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Kehadiran Jarak Jauh (WFA)</span>
                                    <span class="item-sub">
                                        @if($payroll->total_wfa_days > 0)
                                            {{ $payroll->total_wfa_days }} hari WFA @ {{ $wfaPct }} x Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr
                                        @else
                                            0 hari WFA (Bebas potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount">
                                    {{ $payroll->wfa_deduction_amount > 0 ? '- Rp ' . number_format($payroll->wfa_deduction_amount, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>

                            <!-- Item 2: Izin -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Izin Tidak Hadir (Tanpa Upah)</span>
                                    <span class="item-sub">
                                        @if(($payroll->total_permit_days ?? 0) > 0)
                                            {{ $payroll->total_permit_days }} hari Izin @ 100% x Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr
                                        @else
                                            0 hari Izin (Tidak ada potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount">
                                    {{ $permitDeduct > 0 ? '- Rp ' . number_format($permitDeduct, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>

                            <!-- Item 3: Sakit & Sanksi -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Sakit & Sanksi Kedisiplinan</span>
                                    <span class="item-sub">
                                        @if(($payroll->total_sick_days ?? 0) > 0 || $sanctionDeduct > 0)
                                            {{ $payroll->total_sick_days ?? 0 }} hr Sakit · Denda Sanksi: Rp {{ number_format($sanctionDeduct, 0, ',', '.') }}
                                        @else
                                            0 hari Sakit & 0 Sanksi (Tidak ada potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount">
                                    {{ ($sickDeduct + $sanctionDeduct) > 0 ? '- Rp ' . number_format($sickDeduct + $sanctionDeduct, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Total Pemotongan Gaji</td>
                            <td class="item-amount">
                                {{ $totalDeduct > 0 ? '- Rp ' . number_format($totalDeduct, 0, ',', '.') : 'Rp 0' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Kotak Total Gaji Bersih Diterima (Menggunakan Tabel 100% agar tidak pernah terpotong di tepi kanan) -->
    <table class="net-salary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="net-main-left">
                <div class="net-title">TOTAL GAJI BERSIH DITERIMA</div>
                <div class="net-formula">
                    Rumus: Total Penerimaan Kotor (Rp {{ number_format($payroll->gross_salary, 0, ',', '.') }}) dikurangi Total Pemotongan Resmi (Rp {{ number_format($totalDeduct, 0, ',', '.') }})
                </div>
            </td>
            <td class="net-main-right">
                <div class="net-amount">
                    Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="net-terbilang-cell">
                Terbilang: <strong># {{ $terbilang }} #</strong>
            </td>
        </tr>
    </table>

    <!-- Pengesahan & Tanda Tangan -->
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sign-title">Disetujui & Diterbitkan Oleh,</div>
                <div class="sign-name">Divisi Keuangan & Penggajian SDM</div>
                <div class="sign-role">{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</div>
            </td>
            <td>
                <div class="sign-title">Diterima Oleh Personil,</div>
                <div class="sign-name">{{ $user->name ?? 'Karyawan Bersangkutan' }}</div>
                <div class="sign-role">{{ ($user->employment_type ?? '') === 'INTERN' ? 'Magang' : ($user->employment_type ?? 'Karyawan') }} - ChronoHR</div>
            </td>
        </tr>
    </table>

    <!-- Catatan Kaki Dokumen -->
    <div class="footer-note">
        Slip gaji ini diterbitkan secara elektronik oleh sistem ChronoHR pada {{ now()->translatedFormat('d F Y H:i') }} WIB.<br>
        Dokumen ini sah sebagai bukti rincian pembayaran penghasilan karyawan sesuai ketetapan perusahaan.
    </div>

</body>
</html>
