<!-- CHRONOHR PORTAL HEADER -->
<header
    class="h-[72px] bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30 shadow-2xs">

    <!-- Left -->
    <div class="flex items-center gap-3 min-w-0">
        <button type="button" onclick="toggleSidebar()"
            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition border border-slate-200/80 cursor-pointer"
            title="Buka Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">

        <!-- Live Clock Pill (Matches Login Page) -->
        <div
            class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-mono bg-slate-50 border border-slate-200/80">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="liveDate font-sans text-slate-500 hidden xl:inline text-xs">Memuat...</span>
            <span class="text-slate-300 hidden xl:inline">•</span>
            <span class="liveClock font-bold tracking-wider text-slate-900">--:--:--</span>
            <span class="text-[10px] font-bold text-sky-600">WIB</span>
        </div>

        <!-- Notification -->
        <a href="{{ route('admin.dashboard') }}"
            class="w-9 h-9 rounded-xl flex items-center justify-center transition relative bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-600 hover:text-slate-900 cursor-pointer"
            title="Notifikasi">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full ring-2 ring-white bg-sky-600"></span>
        </a>

        <!-- User Dropdown -->
        <div class="relative" id="user-menu-container">
            <button type="button" onclick="toggleUserDropdown(event)"
                class="flex items-center gap-2.5 pl-1.5 pr-3 py-1 rounded-full transition cursor-pointer focus:outline-none bg-slate-50 hover:bg-slate-100 border border-slate-200/80">
                <div class="relative">
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-white font-bold font-mono text-xs bg-slate-900">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span
                        class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                </div>
                <div class="hidden sm:flex flex-col text-left">
                    <span
                        class="text-xs font-bold text-slate-900 max-w-[120px] truncate leading-tight">{{ auth()->user()->name }}</span>
                    <span
                        class="text-[10px] font-medium text-sky-600 truncate">{{ auth()->user()->roles->first()?->display_name ?? 'Staff' }}</span>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150 ml-0.5" id="user-chevron-icon"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="user-dropdown-menu"
                class="absolute right-0 mt-2.5 w-72 rounded-2xl bg-white shadow-xl py-0 z-50 hidden border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-sky-400 font-bold text-sm font-mono bg-white/10 border border-slate-700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 font-mono truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        @foreach (auth()->user()->roles as $role)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-500/20 text-sky-300 border border-sky-400/30">{{ $role->display_name ?? $role->name }}</span>
                        @endforeach
                        @if (auth()->user()->can_approve_leaves)
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Approver</span>
                        @endif
                    </div>
                </div>
                <div class="py-1.5 text-xs">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium">Pengaturan Profil</span>
                    </a>
                    <div class="px-4 py-2 flex items-center justify-between text-[11px] text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Status Akun
                        </span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1"><span
                                class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif</span>
                    </div>
                </div>
                <div class="pt-1 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
