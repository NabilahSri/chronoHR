<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Slip Gaji — {{ $user->name ?? 'Karyawan' }} ({{ $monthName }} {{ $payroll->year }})</title>
    <style>
        @page {
            margin: 18px 24px 18px 24px;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background-color: #ffffff;
            line-height: 1.35;
            padding: 0;
        }

        /* Header Style */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .company-logo {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .company-logo span {
            color: #0284c7;
        }
        .company-sub {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.3;
        }
        .payslip-title {
            text-align: right;
        }
        .payslip-title h1 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payslip-title .badge {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            background-color: #e0f2fe;
            color: #0369a1;
            margin-top: 3px;
        }

        /* Metadata & Employee Info Table */
        .info-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 11px;
            padding: 8px 12px;
            page-break-inside: avoid;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2.5px 5px;
            vertical-align: top;
            font-size: 9.5px;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            width: 18%;
        }
        .info-colon {
            width: 2%;
            color: #94a3b8;
        }
        .info-val {
            color: #0f172a;
            font-weight: 600;
            width: 30%;
        }

        /* Attendance Summary Bar */
        .attendance-summary {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background-color: #ffffff;
            margin-bottom: 12px;
            text-align: center;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .attendance-summary td {
            padding: 6px 4px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .attendance-summary td:last-child {
            border-right: none;
        }
        .att-num {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
        }
        .att-lbl {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        /* Earnings & Deductions Tables */
        .components-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .components-table td {
            vertical-align: top;
            width: 50%;
        }
        .components-table td:first-child {
            padding-right: 6px;
        }
        .components-table td:last-child {
            padding-left: 6px;
        }

        .box-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .box-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 5px 8px;
            text-align: left;
        }
        .box-table th.amount {
            text-align: right;
        }
        .box-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9.5px;
        }
        .box-table tr:last-child td {
            border-bottom: none;
        }
        .box-table .item-name {
            color: #334155;
            font-weight: 600;
            display: block;
        }
        .box-table .item-sub {
            font-size: 8px;
            color: #64748b;
            display: block;
            margin-top: 1px;
            line-height: 1.25;
        }
        .box-table .item-amount {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5px;
            white-space: nowrap;
        }
        .box-table .total-row td {
            background-color: #f8fafc;
            font-weight: 800;
            border-top: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        /* Take Home Pay Box */
        .net-salary-card {
            width: 100%;
            border: 2px solid #0284c7;
            background-color: #f0f9ff;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .net-table {
            width: 100%;
            border-collapse: collapse;
        }
        .net-title {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0369a1;
            letter-spacing: 0.5px;
        }
        .net-amount {
            font-size: 17px;
            font-weight: 800;
            color: #0c4a6e;
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }
        .terbilang-box {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #7dd3fc;
            font-size: 9px;
            font-style: italic;
            color: #0284c7;
            line-height: 1.3;
        }

        /* Signatures Section */
        .signatures-table {
            width: 100%;
            margin-top: 14px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 16px;
        }
        .sign-title {
            font-size: 9px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 34px;
        }
        .sign-name {
            font-size: 10.5px;
            font-weight: 800;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            display: inline-block;
            min-width: 150px;
        }
        .sign-role {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Footer */
        .footer-note {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- Header: Company & Payslip Title -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                <div class="company-logo">Chrono<span>HR</span></div>
                <div class="company-sub">
                    <strong>{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</strong><br>
                    {{ $officeSettings['address'] ?? 'Pusat Operasional Jakarta' }} · Telp: (021) 5082-8888
                </div>
            </td>
            <td style="width: 45%; vertical-align: middle;" class="payslip-title">
                <h1>SLIP GAJI KARYAWAN</h1>
                <div class="badge">CONFIDENTIAL & OFFICIAL</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                    No. Dokumen: <strong>CHR-PAY/{{ $payroll->year }}{{ sprintf('%02d', $payroll->month) }}/{{ strtoupper(substr($payroll->id, 0, 8)) }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Employee Information Card -->
    <div class="info-card">
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-label">Nama Personil</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ $user->name ?? '—' }}</td>

                <td class="info-label">Periode Gaji</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ $monthName }} {{ $payroll->year }}</td>
            </tr>
            <tr>
                <td class="info-label">Email / User ID</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ $user->email ?? '—' }}</td>

                <td class="info-label">Jadwal Penggajian</td>
                <td class="info-colon">:</td>
                <td class="info-val">Tanggal {{ $officeSettings['payday'] ?? 25 }} {{ $monthName }}</td>
            </tr>
            <tr>
                <td class="info-label">Ikatan Kerja</td>
                <td class="info-colon">:</td>
                <td class="info-val">
                    @if(($user->employment_type ?? '') === 'KARTAP')
                        Karyawan Tetap (KARTAP)
                    @elseif(($user->employment_type ?? '') === 'PKWT')
                        Kontrak Waktu Tertentu (PKWT)
                    @elseif(($user->employment_type ?? '') === 'INTERN')
                        Program Magang (INTERN)
                    @else
                        {{ $user->employment_type ?? '—' }}
                    @endif
                </td>

                <td class="info-label">Rekening Transfer</td>
                <td class="info-colon">:</td>
                <td class="info-val">
                    @if($user->bank_name)
                        {{ $user->bank_name }} - {{ $user->bank_account_number }}
                    @else
                        Tunai / Belum Terdaftar
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">Status Verifikasi</td>
                <td class="info-colon">:</td>
                <td class="info-val">
                    <span style="color: {{ $payroll->status === 'PUBLISHED' ? '#059669' : '#d97706' }};">
                        {{ $payroll->status === 'PUBLISHED' ? 'TERVERIFIKASI & RILIS (PUBLISHED)' : 'DRAFT OPERASIONAL' }}
                    </span>
                </td>

                <td class="info-label">Tanggal Cetak</td>
                <td class="info-colon">:</td>
                <td class="info-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Attendance & Working Summary -->
    <table class="attendance-summary" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 16.66%;">
                <div class="att-num">{{ $payroll->total_working_days }} Hari</div>
                <div class="att-lbl">Hari Kerja</div>
            </td>
            <td style="width: 16.66%;">
                <div class="att-num" style="color: #059669;">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Fleksibel' : $payroll->total_present_onsite . ' Hari' }}
                </div>
                <div class="att-lbl">Presensi On-Site</div>
            </td>
            <td style="width: 16.66%;">
                <div class="att-num" style="color: #0284c7;">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : $payroll->total_wfh_days . ' Hari' }}
                </div>
                <div class="att-lbl">Kerja WFH</div>
            </td>
            <td style="width: 16.66%;">
                <div class="att-num" style="color: #6366f1;">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : ($payroll->total_permit_days ?? 0) . ' Hari' }}
                </div>
                <div class="att-lbl">Izin (Unpaid)</div>
            </td>
            <td style="width: 16.66%;">
                <div class="att-num" style="color: #e11d48;">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? 'Bebas' : ($payroll->total_sick_days ?? 0) . ' Hari' }}
                </div>
                <div class="att-lbl">Sakit (Unpaid)</div>
            </td>
            <td style="width: 16.66%;">
                <div class="att-num" style="color: #d97706;">
                    {{ ($user->employment_type ?? '') === 'INTERN' ? '-' : $payroll->total_overtime_hours . ' Jam' }}
                </div>
                <div class="att-lbl">Lembur Resmi</div>
            </td>
        </tr>
    </table>

    <!-- Earnings & Deductions Tables Side-by-Side -->
    <table class="components-table" cellpadding="0" cellspacing="0">
        <tr>
            <!-- Kolom Kiri: PENGHASILAN (EARNINGS) -->
            <td>
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
                                    <span class="item-sub">Nominal sukarela / ketetapan magang</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($user->basic_salary ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Insentif Proyek Magang</span>
                                    <span class="item-sub">Apresiasi kontribusi proyek berjalan</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <span class="item-name">Gaji Pokok Bulanan</span>
                                    <span class="item-sub">Tarif harian acuan @ Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hari</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($user->basic_salary ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Upah Lembur Resmi</span>
                                    <span class="item-sub">{{ $payroll->total_overtime_hours }} jam @ Rp 21.000 / jam</span>
                                </td>
                                <td class="item-amount">
                                    Rp {{ number_format($payroll->overtime_pay, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td style="color: #0f172a;">Total Penghasilan Kotor (Gross)</td>
                            <td class="item-amount" style="color: #059669;">
                                Rp {{ number_format($payroll->gross_salary, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <!-- Kolom Kanan: POTONGAN (DEDUCTIONS) -->
            <td>
                <table class="box-table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="background-color: #334155;">2. Rincian Pemotongan</th>
                            <th class="amount" style="background-color: #334155;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(($user->employment_type ?? '') === 'INTERN')
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Kehadiran Jarak Jauh (WFH)</span>
                                    <span class="item-sub">Bebas pemotongan operasional (Magang)</span>
                                </td>
                                <td class="item-amount" style="color: #64748b;">
                                    Rp 0
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Izin Tidak Masuk</span>
                                    <span class="item-sub">Bebas potongan kehadiran magang</span>
                                </td>
                                <td class="item-amount" style="color: #64748b;">
                                    Rp 0
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Sakit</span>
                                    <span class="item-sub">Bebas potongan kehadiran magang</span>
                                </td>
                                <td class="item-amount" style="color: #64748b;">
                                    Rp 0
                                </td>
                            </tr>
                        @else
                            @php
                                $wfhPct = ($user->employment_type ?? '') === 'PKWT' ? '30%' : '10%';
                                $permitDeduct = ($payroll->total_permit_days ?? 0) * $payroll->daily_salary_rate;
                                $sickDeduct = ($payroll->total_sick_days ?? 0) * $payroll->daily_salary_rate;
                            @endphp
                            <!-- Item 1: WFH -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Kehadiran Jarak Jauh (WFH)</span>
                                    <span class="item-sub">
                                        @if($payroll->total_wfh_days > 0)
                                            {{ $payroll->total_wfh_days }} hari WFH @ {{ $wfhPct }} × Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr
                                        @else
                                            0 hari WFH (Bebas potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount" style="color: {{ $payroll->wfh_deduction_amount > 0 ? '#e11d48' : '#64748b' }};">
                                    {{ $payroll->wfh_deduction_amount > 0 ? '- Rp ' . number_format($payroll->wfh_deduction_amount, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>

                            <!-- Item 2: Izin -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Izin Tidak Hadir (Unpaid)</span>
                                    <span class="item-sub">
                                        @if(($payroll->total_permit_days ?? 0) > 0)
                                            {{ $payroll->total_permit_days }} hari Izin @ 100% × Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr
                                        @else
                                            0 hari Izin (Tidak ada potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount" style="color: {{ $permitDeduct > 0 ? '#e11d48' : '#64748b' }};">
                                    {{ $permitDeduct > 0 ? '- Rp ' . number_format($permitDeduct, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>

                            <!-- Item 3: Sakit -->
                            <tr>
                                <td>
                                    <span class="item-name">Potongan Sakit (Unpaid)</span>
                                    <span class="item-sub">
                                        @if(($payroll->total_sick_days ?? 0) > 0)
                                            {{ $payroll->total_sick_days }} hari Sakit @ 100% × Rp {{ number_format($payroll->daily_salary_rate, 0, ',', '.') }}/hr
                                        @else
                                            0 hari Sakit (Tidak ada potongan)
                                        @endif
                                    </span>
                                </td>
                                <td class="item-amount" style="color: {{ $sickDeduct > 0 ? '#e11d48' : '#64748b' }};">
                                    {{ $sickDeduct > 0 ? '- Rp ' . number_format($sickDeduct, 0, ',', '.') : 'Rp 0' }}
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td style="color: #0f172a;">Total Pemotongan Gaji</td>
                            <td class="item-amount" style="color: #e11d48;">
                                - Rp {{ number_format($payroll->wfh_deduction_amount + ($payroll->leave_deduction_amount ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Take Home Pay (Gaji Bersih Diterima) Box -->
    <div class="net-salary-card">
        <table class="net-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="vertical-align: middle; width: 62%;">
                    <div class="net-title">TOTAL TAKE HOME PAY (GAJI BERSIH DITERIMA)</div>
                    <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">
                        Formula: Total Penerimaan Kotor - Total Pemotongan Resmi (WFH & Izin/Sakit)
                    </div>
                </td>
                <td style="vertical-align: middle; width: 38%;" class="net-amount">
                    Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        <div class="terbilang-box">
            Terbilang: <strong>{{ $terbilang }}</strong>
        </div>
    </div>

    <!-- Signatures & Authority -->
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sign-title">Disetujui & Diterbitkan Oleh,</div>
                <div class="sign-name">Divisi Finance & HR Payroll</div>
                <div class="sign-role">{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</div>
            </td>
            <td>
                <div class="sign-title">Diterima Oleh Personil,</div>
                <div class="sign-name">{{ $user->name ?? 'Karyawan Bersangkutan' }}</div>
                <div class="sign-role">{{ $user->employment_type ?? 'Karyawan' }} · ChronoHR</div>
            </td>
        </tr>
    </table>

    <!-- Footer Disclaimer -->
    <div class="footer-note">
        Dokumen ini dibuat dan divalidasi secara digital melalui ChronoHR Automated Enterprise Payroll Engine.<br>
        Informasi yang tertera di dalam dokumen ini bersifat rahasia (CONFIDENTIAL) dan hanya diperuntukkan bagi personil yang bersangkutan.
    </div>

</body>
</html>
