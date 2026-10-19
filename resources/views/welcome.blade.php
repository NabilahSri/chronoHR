<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoHR — Precision Time, Effortless Payroll</title>

    <!-- Favicon SVG -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='16' fill='%230F172A'/><circle cx='32' cy='32' r='23' stroke='%2338BDF8' stroke-width='2.5' fill='none'/><rect x='22' y='28' width='5' height='10' rx='2' fill='%2338BDF8'/><rect x='30' y='21' width='5' height='17' rx='2' fill='%2338BDF8'/><rect x='38' y='14' width='5' height='24' rx='2' fill='%2338BDF8'/><path d='M21 28l9 9 16-19' stroke='%23FFFFFF' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/></svg>">

    <!-- Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-slate-50 text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white flex flex-col justify-between">

    <!-- Top Navigation -->
    <header class="w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-2xs">
        <div class="max-w-7xl mx-auto px-6 h-[72px] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-slate-900 p-1.5 flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="32" r="23" stroke="#CBD5E1" stroke-width="2" stroke-dasharray="2 3" />
                        <path d="M 32 9 A 23 23 0 0 1 55 32" stroke="#38BDF8" stroke-width="3" stroke-linecap="round" />
                        <rect x="15" y="37" width="34" height="15" rx="3.5" fill="#0F172A" stroke="#38BDF8" stroke-width="1.5" />
                        <rect x="23" y="27" width="4.5" height="9" rx="1.5" fill="#38BDF8" />
                        <rect x="29.8" y="20.5" width="4.5" height="15.5" rx="1.5" fill="#38BDF8" />
                        <rect x="36.5" y="14" width="4.5" height="22" rx="1.5" fill="#38BDF8" />
                        <path d="M 21.5 27.5 L 29.5 35.5 L 45.5 15.5" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold tracking-tight text-slate-900">Chrono<span class="text-sky-600">HR</span></span>
                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-sky-100/70 text-sky-800 border border-sky-200/80 ml-1.5">Ecosystem</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center gap-2">
                        <span>Masuk ke Portal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Content -->
    <main class="flex-1 max-w-5xl mx-auto px-6 py-16 sm:py-24 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100/70 border border-sky-200/80 text-sky-800 text-xs font-semibold mb-6">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Generasi Baru HRMS & Otomasi Payroll Berbasis IoT</span>
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15] max-w-3xl">
            Presisi Waktu Kerja. <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 via-sky-700 to-slate-900">
                Otomasi Penggajian Nyata.
            </span>
        </h1>

        <p class="mt-6 text-sm sm:text-base text-slate-600 max-w-2xl leading-relaxed">
            Satu ekosistem terpadu yang menyatukan pencatatan presensi RFID secara presisi, transparansi perizinan karyawan, dan kalkulasi payroll instan bebas rekonsiliasi manual.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('login') }}"
                class="px-6 py-3 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-semibold rounded-xl shadow-sm hover:shadow transition-all text-sm flex items-center gap-2">
                <span>Akses Portal Admin</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>

        <!-- 3 Core Filosofi Pillars -->
        <div class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 w-full text-left">
            <div class="p-6 rounded-3xl bg-white/95 border border-slate-200/90 shadow-2xs backdrop-blur-xs">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">RFID Real-Time</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Sinkronisasi tap kartu instan melalui mikrokontroler NodeMCU ESP8266.</p>
            </div>

            <div class="p-6 rounded-3xl bg-white/95 border border-slate-200/90 shadow-2xs backdrop-blur-xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Payroll Otomatis</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Kalkulasi lembur, potongan WFH, dan insentif magang tergenerasi presisi.</p>
            </div>

            <div class="p-6 rounded-3xl bg-white/95 border border-slate-200/90 shadow-2xs backdrop-blur-xs">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1">Approval Cepat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Alur verifikasi perizinan dan cuti terintegrasi langsung dengan absensi.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full bg-white border-t border-slate-200/80 py-6 px-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">Chrono<span class="text-sky-600">HR</span></span>
                <span>• © 2026 PT Chrono Presisi Indonesia</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400">
                <span>SSL 256-Bit Encrypted</span>
                <span>•</span>
                <span>ISO 27001 Security Standard</span>
            </div>
        </div>
    </footer>

</body>

</html>
