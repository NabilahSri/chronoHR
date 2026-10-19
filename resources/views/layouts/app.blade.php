<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ChronoHR — HRMS & Workforce Management')</title>

    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='16' fill='%230F172A'/><circle cx='32' cy='32' r='23' stroke='%2338BDF8' stroke-width='2.5' fill='none'/><rect x='22' y='28' width='5' height='10' rx='2' fill='%2338BDF8'/><rect x='30' y='21' width='5' height='17' rx='2' fill='%2338BDF8'/><rect x='38' y='14' width='5' height='24' rx='2' fill='%2338BDF8'/><path d='M21 28l9 9 16-19' stroke='%23FFFFFF' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-slate-50 text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white">
    <div class="h-screen w-full flex overflow-hidden">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">

            @include('layouts.header')

            <main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-50 via-sky-50/25 to-slate-100 flex flex-col justify-between">
                <div>
                    @if (session('success') || session('error'))
                        <div class="px-6 sm:px-8 pt-6">
                            @if (session('success'))
                                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3 shadow-2xs mb-2 animate-slide-in-up">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="font-semibold">{{ session('success') }}</span>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-3 shadow-2xs mb-2 animate-slide-in-up">
                                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="font-semibold">{{ session('error') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="p-6 sm:p-8">
                        @hasSection('page_title')
                            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                        @yield('page_title')
                                    </h1>
                                    @hasSection('page_subtitle')
                                        <p class="text-xs sm:text-sm text-slate-500 mt-1">@yield('page_subtitle')</p>
                                    @endif
                                </div>
                                @hasSection('page_actions')
                                    <div class="flex items-center gap-2.5 shrink-0">
                                        @yield('page_actions')
                                    </div>
                                @endif
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>

                @include('layouts.footer')
            </main>
        </div>
    </div>

    <script>
        function updateLiveClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.querySelectorAll('.liveClock').forEach(el => el.textContent = `${hours}:${minutes}:${seconds}`);
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Jakarta' };
            try {
                const dateStr = now.toLocaleDateString('id-ID', dateOptions);
                document.querySelectorAll('.liveDate').forEach(el => el.textContent = dateStr);
            } catch (e) {
                document.querySelectorAll('.liveDate').forEach(el => el.textContent = now.toDateString());
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function toggleUserDropdown(event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('user-dropdown-menu');
            const chevron = document.getElementById('user-chevron-icon');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        function toggleReportsDropdown() {
            const submenu = document.getElementById('reports-submenu');
            const chevron = document.getElementById('reports-chevron-icon');
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                    chevron.classList.toggle('text-sky-400');
                }
            }
        }

        document.addEventListener('click', function(e) {
            const container = document.getElementById('user-menu-container');
            const menu = document.getElementById('user-dropdown-menu');
            const chevron = document.getElementById('user-chevron-icon');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });

        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    </script>
</body>
</html>
