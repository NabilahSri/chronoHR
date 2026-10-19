<!-- Mobile Backdrop -->
<div id="sidebar-backdrop" onclick="toggleSidebar()"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 hidden lg:hidden"></div>

<aside id="app-sidebar"
    class="fixed inset-y-0 left-0 z-50 h-full flex flex-col shrink-0 transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:h-full select-none border-r border-slate-800/80 shadow-xl lg:shadow-none"
    style="width:272px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);">

    <!-- Brand Header -->
    <div class="h-[72px] flex items-center justify-between px-5 border-b border-slate-800/80">
        <div class="flex items-center gap-3">
            <!-- Official Unified ChronoHR Logo SVG -->
            <div
                class="w-10 h-10 rounded-2xl bg-white/95 border border-slate-200/20 shadow-sm p-1.5 flex items-center justify-center shrink-0">
                <svg class="w-full h-full" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Chronometer Dial Ring -->
                    <circle cx="32" cy="32" r="23" stroke="#CBD5E1" stroke-width="1.8"
                        stroke-dasharray="2 3" opacity="0.9" />
                    <path d="M 32 9 A 23 23 0 0 1 55 32" stroke="#0284C7" stroke-width="2.6" stroke-linecap="round" />
                    <circle cx="32" cy="9" r="2.2" fill="#0284C7" />

                    <!-- RFID Card Slot -->
                    <rect x="15" y="37" width="34" height="15" rx="3.5" fill="#0F172A" stroke="#0284C7"
                        stroke-width="1.2" />
                    <path d="M 20.5 42 A 3.5 3.5 0 0 1 20.5 47" stroke="#38BDF8" stroke-width="1.3"
                        stroke-linecap="round" fill="none" />
                    <path d="M 23 40.5 A 6 6 0 0 1 23 48.5" stroke="#38BDF8" stroke-width="1.3" stroke-linecap="round"
                        fill="none" opacity="0.65" />
                    <rect x="36" y="41.5" width="8.5" height="6" rx="1.2" fill="#0284C7" opacity="0.35"
                        stroke="#38BDF8" stroke-width="0.8" />

                    <!-- Growth Bars -->
                    <rect x="23" y="27" width="4.5" height="9" rx="1.5" fill="#0284C7" opacity="0.75" />
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
                    <span class="text-base font-bold tracking-tight text-white">Chrono<span
                            class="text-sky-400">HR</span></span>
                    <span
                        class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md text-sky-300 bg-sky-500/15 border border-sky-400/30">Portal</span>
                </div>
                <p class="text-[10px] text-slate-400 font-medium">Precision Time & Payroll</p>
            </div>
        </div>
        <button type="button" onclick="toggleSidebar()"
            class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800/80 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- System Status Banner -->
    <div class="px-4 py-2.5 border-b border-slate-800/60 bg-black/25">
        <div class="flex items-center justify-between">
            <span class="text-[11px] text-slate-300 font-medium flex items-center gap-2">
                <span class="relative flex h-1.5 w-1.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                </span>
                RFID Fleet: NodeMCU
            </span>
            <span
                class="text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-full font-mono text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">LIVE</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

        @if (auth()->user()->hasRole('super_admin'))
            <div class="px-3 pt-2 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                System Administration
            </div>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('admin.dashboard') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
                <span class="text-sm">Dashboard</span>
                @if (request()->routeIs('admin.dashboard'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('admin.users.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-sm">Kelola Karyawan</span>
                @if (request()->routeIs('admin.users.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.attendances.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.attendances.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('admin.attendances.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-sm">Log Presensi</span>
                @if (request()->routeIs('admin.attendances.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.settings.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.settings.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('admin.settings.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-sm">Pengaturan</span>
                @if (request()->routeIs('admin.settings.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>

            <a href="{{ route('admin.devices.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('admin.devices.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('admin.devices.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <span class="text-sm">Status & Alat IoT</span>
                @if (request()->routeIs('admin.devices.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>
        @endif

        @if (auth()->user()->hasRole('ceo'))
            <div class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                Executive Portal
            </div>

            <a href="{{ route('executive.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('executive.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('executive.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-sm">Dashboard Eksekutif</span>
                @if (request()->routeIs('executive.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>
        @endif

        @if (auth()->user()->hasRole('finance'))
            <div class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                Finance & Payroll
            </div>

            <a href="{{ route('finance.payroll.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('finance.payroll.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('finance.payroll.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm">Kalkulasi Payroll</span>
                @if (request()->routeIs('finance.payroll.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>

            <a href="{{ route('finance.incentives.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('finance.incentives.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('finance.incentives.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-sm">Insentif Magang</span>
                @if (request()->routeIs('finance.incentives.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>
        @endif

        @if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('ceo') || auth()->user()->hasRole('finance'))
            <div class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                Pusat Data
            </div>

            <!-- Dropdown Menu Laporan -->
            <div class="space-y-1">
                <button type="button" onclick="toggleReportsDropdown()"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-all cursor-pointer {{ request()->routeIs('reports.*') ? 'text-white bg-slate-800/80 border border-slate-700/60 font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('reports.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm">Menu Laporan</span>
                    </div>
                    <svg id="reports-chevron-icon"
                        class="w-4 h-4 text-slate-400 transition-transform duration-200 {{ request()->routeIs('reports.*') ? 'rotate-180 text-sky-400' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Submenu Items -->
                <div id="reports-submenu"
                    class="pl-11 pr-2 py-1 space-y-1 {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                    <a href="{{ route('reports.attendances') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('reports.attendances') ? 'text-white bg-sky-500/20 font-bold border border-sky-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <span>Log Presensi</span>
                        @if (request()->routeIs('reports.attendances'))
                            <div class="w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('reports.payrolls') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('reports.payrolls') ? 'text-white bg-sky-500/20 font-bold border border-sky-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <span>Slip Gaji</span>
                        @if (request()->routeIs('reports.payrolls'))
                            <div class="w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('reports.leaves') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('reports.leaves') ? 'text-white bg-sky-500/20 font-bold border border-sky-400/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                        <span>Pengajuan</span>
                        @if (request()->routeIs('reports.leaves'))
                            <div class="w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                            </div>
                        @endif
                    </a>
                </div>
            </div>
        @endif

        @if (auth()->user()->can_approve_leaves || auth()->user()->hasRole('super_admin'))
            <div class="px-3 pt-4 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                Approval Workflow
            </div>

            <a href="{{ route('approval.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('approval.*') ? 'text-white bg-sky-500/20 border border-sky-400/30 font-semibold shadow-2xs' : 'text-slate-300 hover:text-white hover:bg-slate-800/60 border border-transparent' }}">
                <div
                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ request()->routeIs('approval.*') ? 'bg-sky-500/30 text-sky-300 border border-sky-400/30' : 'bg-slate-800/70 text-slate-400 border border-slate-700/40' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm">Persetujuan Cuti</span>
                @if (request()->routeIs('approval.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-400 shadow-[0_0_8px_rgba(56,189,248,0.7)]">
                    </div>
                @endif
            </a>
        @endif
    </nav>
</aside>
