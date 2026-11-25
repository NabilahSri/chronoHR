<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Peringatan - {{ $user->name ?? 'Karyawan' }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 36px 44px;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            background-color: #ffffff;
            line-height: 1.55;
            margin: 0;
            padding: 14px 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            border-bottom: 2px solid #111827;
            margin-bottom: 20px;
        }
        .header-table td {
            padding-bottom: 12px;
            vertical-align: top;
        }
        .company-brand {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
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
            letter-spacing: 0.6px;
        }
        .doc-number {
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 4px;
            font-family: 'Courier New', Courier, monospace;
        }
        .letter-heading {
            text-align: center;
            margin-bottom: 18px;
        }
        .letter-heading h1 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
            color: #111827;
        }
        .letter-heading p {
            font-size: 9px;
            color: #374151;
            margin: 4px 0 0 0;
            font-family: 'Courier New', Courier, monospace;
        }
        .info-box {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            margin-bottom: 16px;
        }
        .info-box td {
            padding: 6px 10px;
            font-size: 9.5px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .info-box tr:last-child td {
            border-bottom: none;
        }
        .info-label {
            color: #4b5563;
            width: 28%;
        }
        .info-colon {
            width: 3%;
            text-align: center;
            color: #6b7280;
        }
        .info-val {
            font-weight: bold;
            color: #111827;
            width: 69%;
        }
        .paragraph {
            margin-bottom: 12px;
            text-align: justify;
            font-size: 10px;
            color: #111827;
        }
        .violation-box {
            border: 1.5px solid #111827;
            background-color: #f9fafb;
            margin-bottom: 16px;
        }
        .violation-box th {
            background-color: #f3f4f6;
            color: #111827;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 10px;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
        }
        .violation-box td {
            padding: 9px 10px;
            font-size: 9.5px;
            vertical-align: top;
        }
        .signatures-table {
            margin-top: 28px;
            margin-bottom: 20px;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sign-title {
            font-size: 9.5px;
            color: #374151;
            margin-bottom: 52px;
        }
        .sign-name {
            font-size: 10px;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #374151;
            padding-top: 5px;
            display: inline-block;
            min-width: 175px;
        }
        .sign-role {
            font-size: 8.5px;
            color: #6b7280;
            margin-top: 2px;
        }
        .footer-note {
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
            line-height: 1.45;
        }
    </style>
</head>
<body>
    @php
        $docNumber = 'CHR-SP/' . $sanction->year . sprintf('%02d', $sanction->month) . '/' . strtoupper(substr($sanction->id, 0, 8));
        $levelTitle = \App\Models\SanctionRule::levelLabel($sanction->level);
    @endphp

    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%;">
                <div class="company-brand">{{ $appBrandName ?? 'ChronoHR' }}</div>
                <div class="company-meta">
                    <strong>{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</strong> · {{ $appBrandSlogan ?? 'Presisi Waktu & Penggajian' }}<br>
                    {{ $officeSettings['address'] ?? 'Gedung Pusat ChronoHR, Jakarta' }}
                </div>
            </td>
            <td style="width: 40%;" class="doc-header-right">
                <div class="doc-title">DOKUMEN PEMBINAAN SDM</div>
                <div class="doc-number">Status: {{ $sanction->status === 'ACTIVE' ? 'AKTIF BERLAKU' : $sanction->status }}</div>
            </td>
        </tr>
    </table>

    <div class="letter-heading">
        <h1>SURAT {{ strtoupper($levelTitle) }}</h1>
        <p>Nomor Dokumen: {{ $docNumber }}</p>
    </div>

    <div class="paragraph">
        Surat keputusan pembinaan kedisiplinan ini diterbitkan oleh Manajemen Sumber Daya Manusia &amp; Keuangan
        <strong>{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</strong> kepada personil di bawah ini:
    </div>

    <table class="info-box" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-label">Nama Lengkap Personil</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Alamat Email Terdaftar</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ $user->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Status Ikatan Kerja</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ ($user->employment_type ?? '') === 'INTERN' ? 'Program Magang / PKL' : ($user->employment_type ?? '-') }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Penindakan</td>
            <td class="info-colon">:</td>
            <td class="info-val">{{ \Carbon\Carbon::parse($sanction->incident_date)->translatedFormat('d F Y') }} (Periode {{ $sanction->month }}/{{ $sanction->year }})</td>
        </tr>
    </table>

    <div class="paragraph">
        Berdasarkan hasil pemantauan kedisiplinan dan evaluasi sistem presensi serta operasional perusahaan, personil yang bersangkutan tercatat melakukan pelanggaran terhadap ketetapan perusahaan dengan rincian sebagai berikut:
    </div>

    <table class="violation-box" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Rincian Pelanggaran &amp; Ketetapan Sanksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div><strong>Jenis Pelanggaran:</strong> {{ $sanction->violation_name }}</div>
                    <div style="margin-top: 4px;"><strong>Tingkat Sanksi:</strong> {{ $levelTitle }}</div>
                    <div style="margin-top: 4px;">
                        <strong>Sumber Pencatatan:</strong>
                        {{ $sanction->source === 'AUTO_DETECTED' ? 'Deteksi Otomatis Sistem Presensi (' . $sanction->occurrence_count . ' kali kejadian)' : 'Evaluasi Langsung Manajemen / HRD' }}
                    </div>
                    <div style="margin-top: 4px;">
                        <strong>Konsekuensi Finansial (Slip Gaji):</strong>
                        @if ($sanction->deduction_amount > 0)
                            Potongan Denda Kedisiplinan sebesar <strong>Rp {{ number_format($sanction->deduction_amount, 0, ',', '.') }}</strong> pada periode {{ $sanction->month }}/{{ $sanction->year }}
                        @else
                            Rp 0 (Pembinaan Administratif tanpa pemotongan gaji)
                        @endif
                    </div>
                    <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #d1d5db;">
                        <strong>Keterangan / Kronologi:</strong><br>
                        {{ $sanction->reason }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="paragraph">
        Surat {{ $levelTitle }} ini bertujuan sebagai sarana pembinaan dan evaluasi agar personil yang bersangkutan dapat memperbaiki kedisiplinan kerja serta mematuhi seluruh Peraturan Perusahaan yang berlaku. Apabila di kemudian hari kembali ditemukan pelanggaran serupa, perusahaan berhak menjatuhkan sanksi tingkat berikutnya sesuai ketentuan yang berlaku.
    </div>

    <div class="paragraph">
        Demikian surat ini diterbitkan untuk dapat diperhatikan dan dilaksanakan sebagaimana mestinya.
    </div>

    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sign-title">Diterbitkan Oleh Manajemen,</div>
                <div class="sign-name">{{ $sanction->issuer?->name ?? 'Divisi SDM & Keuangan' }}</div>
                <div class="sign-role">{{ $officeSettings['name'] ?? 'PT ChronoHR Teknologi Indonesia' }}</div>
            </td>
            <td>
                <div class="sign-title">Diterima &amp; Dipahami Oleh,</div>
                <div class="sign-name">{{ $user->name ?? 'Personil Bersangkutan' }}</div>
                <div class="sign-role">{{ ($user->employment_type ?? '') === 'INTERN' ? 'Magang' : ($user->employment_type ?? 'Karyawan') }} - ChronoHR</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini diterbitkan secara elektronik melalui sistem ChronoHR pada {{ now()->translatedFormat('d F Y H:i') }} WIB.<br>
        Salinan resmi tersimpan di dalam basis data kepegawaian dan dapat diakses melalui portal web maupun aplikasi mobile karyawan.
    </div>
</body>
</html>
