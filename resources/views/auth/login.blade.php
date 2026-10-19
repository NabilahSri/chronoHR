<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoHR — Precision Time, Effortless Payroll | Portal Login</title>

    <!-- Favicon SVG -->
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='16' fill='%230F172A'/><circle cx='32' cy='32' r='23' stroke='%2338BDF8' stroke-width='2.5' fill='none'/><rect x='22' y='28' width='5' height='10' rx='2' fill='%2338BDF8'/><rect x='30' y='21' width='5' height='17' rx='2' fill='%2338BDF8'/><rect x='38' y='14' width='5' height='24' rx='2' fill='%2338BDF8'/><path d='M21 28l9 9 16-19' stroke='%23FFFFFF' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/></svg>">

    <!-- Fonts: Plus Jakarta Sans for modern SaaS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-slate-50 text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white">

    <!-- Mobile Top Bar: Visible only on < lg screens -->
    <header
        class="lg:hidden flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-2xs">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-slate-900 p-1 flex items-center justify-center shrink-0">
                <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="32" cy="32" r="23" stroke="#CBD5E1" stroke-width="2"
                        stroke-dasharray="2 3" />
                    <path d="M 32 9 A 23 23 0 0 1 55 32" stroke="#38BDF8" stroke-width="3" stroke-linecap="round" />
                    <rect x="15" y="37" width="34" height="15" rx="3.5" fill="#0F172A" stroke="#38BDF8"
                        stroke-width="1.5" />
                    <rect x="23" y="27" width="4.5" height="9" rx="1.5" fill="#38BDF8" />
                    <rect x="29.8" y="20.5" width="4.5" height="15.5" rx="1.5" fill="#38BDF8" />
                    <rect x="36.5" y="14" width="4.5" height="22" rx="1.5" fill="#38BDF8" />
                    <path d="M 21.5 27.5 L 29.5 35.5 L 45.5 15.5" stroke="#FFFFFF" stroke-width="3.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-slate-900">Chrono<span
                        class="text-sky-600">HR</span></span>
            </div>
        </div>

        <!-- Live Clock Pill on Mobile -->
        <div
            class="flex items-center gap-2 bg-slate-100/90 border border-slate-200/80 px-2.5 py-1 rounded-full text-xs font-mono">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="liveClock font-semibold text-slate-800">--:--:--</span>
            <span class="text-[10px] text-sky-600 font-bold">WIB</span>
        </div>
    </header>

    <div
        class="min-h-screen lg:h-screen flex flex-col lg:grid lg:grid-cols-12 relative overflow-x-hidden lg:overflow-hidden">

        <!-- ======================================================== -->
        <!-- LEFT PANEL: Brand Showcase, Live Chrono, & Value Pillars -->
        <!-- ======================================================== -->
        <section
            class="hidden lg:flex lg:col-span-7 xl:col-span-7 relative flex-col justify-between p-8 xl:p-12 2xl:p-16 bg-gradient-to-br from-slate-50 via-sky-50/25 to-slate-100 border-r border-slate-200/80 overflow-hidden">

            <!-- Subtle Tech Background Grid Pattern & Ambient Glows -->
            <div class="absolute inset-0 bg-grid-pattern opacity-70 pointer-events-none"></div>
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-200/30 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -bottom-28 right-0 w-80 h-80 bg-slate-200/50 rounded-full blur-3xl pointer-events-none">
            </div>

            <!-- Top Row: Brand & System Status -->
            <div class="relative z-10 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <!-- Unified Logo: Clock Dial + RFID Card Slot + Growth Bars + Checkmark -->
                    <div
                        class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-sm p-2 flex items-center justify-center shrink-0">
                        <svg class="w-full h-full" viewBox="0 0 64 64" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <!-- Chronometer Dial Ring -->
                            <circle cx="32" cy="32" r="23" stroke="#CBD5E1" stroke-width="1.8"
                                stroke-dasharray="2 3" opacity="0.9" />
                            <path d="M 32 9 A 23 23 0 0 1 55 32" stroke="#0284C7" stroke-width="2.6"
                                stroke-linecap="round" />
                            <circle cx="32" cy="9" r="2.2" fill="#0284C7" />

                            <!-- RFID Card Slot -->
                            <rect x="15" y="37" width="34" height="15" rx="3.5" fill="#0F172A"
                                stroke="#0284C7" stroke-width="1.2" />
                            <path d="M 20.5 42 A 3.5 3.5 0 0 1 20.5 47" stroke="#38BDF8" stroke-width="1.3"
                                stroke-linecap="round" fill="none" />
                            <path d="M 23 40.5 A 6 6 0 0 1 23 48.5" stroke="#38BDF8" stroke-width="1.3"
                                stroke-linecap="round" fill="none" opacity="0.65" />
                            <rect x="36" y="41.5" width="8.5" height="6" rx="1.2" fill="#0284C7"
                                opacity="0.35" stroke="#38BDF8" stroke-width="0.8" />

                            <!-- Growth Bars -->
                            <rect x="23" y="27" width="4.5" height="9" rx="1.5" fill="#0284C7"
                                opacity="0.75" />
                            <rect x="29.8" y="20.5" width="4.5" height="15.5" rx="1.5" fill="#0284C7"
                                opacity="0.9" />
                            <rect x="36.5" y="14" width="4.5" height="22" rx="1.5" fill="#0284C7" />

                            <!-- Checkmark Flow -->
                            <path d="M 21.5 27.5 L 29.5 35.5 L 45.5 15.5" stroke="#0F172A" stroke-width="3.4"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-bold tracking-tight text-slate-900">Chrono<span
                                    class="text-sky-600">HR</span></span>
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-200/70 text-slate-700"></span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Precision Time, Effortless Payroll.</p>
                    </div>
                </div>

                <!-- Live System Indicator -->
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/95 border border-slate-200/90 shadow-2xs text-xs text-slate-600">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-medium">Sistem Sinkron Aktif</span>
                </div>
            </div>

            <!-- Middle Section: Hero Value Proposition & Live Chrono Widget -->
            <div class="relative z-10 my-auto py-6 max-w-xl">
                <!-- Badge Pill -->
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100/70 border border-sky-200/80 text-sky-800 text-xs font-semibold mb-4">
                    <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Generasi Baru Manajemen SDM</span>
                </div>

                <h1 class="text-3xl xl:text-4xl font-extrabold tracking-tight text-slate-900 leading-[1.25]">
                    Presisi Waktu Kerja. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-600 via-sky-700 to-slate-900">
                        Otomasi Penggajian Nyata.
                    </span>
                </h1>

                <p class="mt-4 text-sm xl:text-base text-slate-600 leading-relaxed">
                    Satu ekosistem terpadu yang menyatukan pencatatan presensi RFID secara presisi, transparansi
                    perizinan karyawan, dan kalkulasi payroll instan bebas rekonsiliasi manual.
                </p>

                <!-- Live Chrono Widget: Precision Time Real-time Indicator -->
                <div
                    class="mt-7 p-4 xl:p-5 rounded-2xl bg-white/95 border border-slate-200/90 shadow-sm backdrop-blur-xs">
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div
                                class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <path stroke-linecap="round" stroke-width="2" d="M12 7v5l3 2" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Waktu
                                    Server Presisi</span>
                                <span class="liveDate text-xs font-medium text-slate-700">Memuat tanggal...</span>
                            </div>
                        </div>

                        <!-- Digital Clock -->
                        <div
                            class="flex items-baseline gap-1.5 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-xl font-mono">
                            <span
                                class="liveClock text-base sm:text-lg font-bold text-slate-900 tracking-wider">--:--:--</span>
                            <span class="text-[11px] font-semibold text-sky-600">WIB</span>
                        </div>
                    </div>

                    <!-- 3 Core Filosofi Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                        <!-- 1. Presisi Waktu & RFID -->
                        <div
                            class="p-2.5 rounded-xl bg-slate-50/90 border border-slate-200/60 flex items-start gap-2.5">
                            <div
                                class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-800 block">RFID Real-Time</span>
                                <span class="text-[11px] text-slate-500">Tap kartu instan tanpa antrean</span>
                            </div>
                        </div>

                        <!-- 2. Otomasi Payroll -->
                        <div
                            class="p-2.5 rounded-xl bg-slate-50/90 border border-slate-200/60 flex items-start gap-2.5">
                            <div
                                class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-800 block">Payroll Otomatis</span>
                                <span class="text-[11px] text-slate-500">Lembur, BPJS & PPh 21 akurat</span>
                            </div>
                        </div>

                        <!-- 3. Seamless Approval -->
                        <div
                            class="p-2.5 rounded-xl bg-slate-50/90 border border-slate-200/60 flex items-start gap-2.5">
                            <div
                                class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-slate-800 block">Approval Cepat</span>
                                <span class="text-[11px] text-slate-500">Workflow izin & cuti transparan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Trust & Security Indicators -->
            <div
                class="relative z-10 pt-4 border-t border-slate-200/70 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 font-medium text-slate-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        SSL 256-Bit Encrypted
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-600">ISO 27001 Security Standard</span>
                </div>
                <span class="text-slate-400">© 2026 ChronoHR Ecosystem</span>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- RIGHT PANEL: Authentication Form Center -->
        <!-- ========================================== -->
        <main
            class="lg:col-span-5 xl:col-span-5 flex flex-col justify-center items-center p-6 sm:p-10 lg:p-10 xl:p-12 bg-white relative z-10 lg:overflow-y-auto">

            <div class="w-full max-w-md my-auto space-y-6">

                <!-- Header for Portal Login -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Portal Administrator
                        </span>
                        <span class="text-xs text-slate-400 font-mono">v2.4.0</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                        Masuk ke Dashboard
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Selamat datang kembali! Silakan masuk ke akun Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Session Flash Notification -->
                @if (session('success'))
                    <div
                        class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-3">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Error Notification -->
                @if ($errors->any())
                    <div
                        class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-3">
                        <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold block text-rose-800">Login Gagal</span>
                            <ul class="mt-1 list-disc list-inside space-y-0.5 text-rose-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="email"
                            class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Alamat Email
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                autofocus placeholder="admin@email.com"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kata Sandi
                            </label>
                            <button type="button" onclick="showHelpModal()"
                                class="text-xs text-sky-600 hover:text-sky-700 font-semibold transition-colors cursor-pointer">
                                Lupa Kata Sandi?
                            </button>
                        </div>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">

                            <button type="button" onclick="togglePassword()" aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition-colors cursor-pointer">
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button: Solid Deep Navy (#0F172A) -->
                    <button type="submit"
                        class="w-full py-3 px-4 mt-2 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-semibold rounded-xl shadow-sm hover:shadow transition-all text-sm flex items-center justify-center gap-2 group cursor-pointer">
                        <span>Masuk</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Employee Mobile Access Card -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                        <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-xs text-slate-500 leading-relaxed">
                        <span class="font-bold text-slate-800 block mb-0.5">Bukan Administrator?</span>
                        Presensi mandiri harian & pengajuan cuti/izin karyawan dilakukan melalui
                        <span class="text-sky-600 font-semibold">Aplikasi Mobile ChronoHR</span>.
                    </div>
                </div>

                <!-- Mobile Footer (Visible only on < lg screens) -->
                <div class="lg:hidden text-center pt-2 text-xs text-slate-400 space-y-1">
                    <p>&copy; 2026 ChronoHR Ecosystem. All rights reserved.</p>
                </div>

            </div>
        </main>

    </div>

    <!-- Help Modal Dialog -->
    <div id="helpModal"
        class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 border border-sky-100 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Bantuan Akses Portal</h3>
                        <p class="text-xs text-slate-500">ChronoHR Identity & Access Management</p>
                    </div>
                </div>
                <button type="button" onclick="closeHelpModal()"
                    class="text-slate-400 hover:text-slate-700 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Portal web ini dikhususkan bagi pemegang peran <strong>Super Admin</strong>, <strong>CEO</strong>, dan
                <strong>Finance</strong> untuk memantau absensi, menyetujui izin, dan memproses payroll.
            </p>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-2">
                <div class="flex items-start gap-2">
                    <span class="text-sky-600 font-bold">•</span>
                    <p><strong>Lupa Kata Sandi:</strong> Hubungi Departemen IT internal perusahaan Anda untuk melakukan
                        pemulihan akses akun.</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-sky-600 font-bold">•</span>
                    <p><strong>Akses Karyawan Umum:</strong> Karyawan staf tidak login melalui portal ini, melainkan via
                        aplikasi mobile ChronoHR.</p>
                </div>
            </div>

            <button type="button" onclick="closeHelpModal()"
                class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                Mengerti & Kembali ke Login
            </button>
        </div>
    </div>

    <!-- Client-Side JavaScript -->
    <script>
        // Real-time Precision Chronometer Clock
        function updateLiveClock() {
            const now = new Date();

            // Format Clock: HH:MM:SS
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}:${seconds}`;

            document.querySelectorAll('.liveClock').forEach(el => {
                el.textContent = timeStr;
            });

            // Format Indonesian Date: Hari, DD MMMM YYYY
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Asia/Jakarta'
            };

            try {
                const dateStr = now.toLocaleDateString('id-ID', dateOptions);
                document.querySelectorAll('.liveDate').forEach(el => {
                    el.textContent = dateStr;
                });
            } catch (e) {
                document.querySelectorAll('.liveDate').forEach(el => {
                    el.textContent = now.toDateString();
                });
            }
        }

        // Start clock immediately & update every second
        updateLiveClock();
        setInterval(updateLiveClock, 1000);

        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.007 10.007 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.72-3.72a3 3 0 11-4.243-4.243M3 3l18 18" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        // Help Modal Controls
        function showHelpModal() {
            const modal = document.getElementById('helpModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeHelpModal() {
            const modal = document.getElementById('helpModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // Close modal on Escape key press
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeHelpModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('helpModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeHelpModal();
            }
        });
    </script>
</body>

</html>
