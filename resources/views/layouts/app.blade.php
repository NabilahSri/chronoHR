<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($appBrandName ?? 'ChronoHR') . ' - ' . ($appBrandSlogan ?? 'Presisi Waktu & Penggajian'))</title>

    @if (!empty($appBrandLogoUrl))
        <link rel="icon" href="{{ $appBrandLogoUrl }}">
    @else
        <link rel="icon" type="image/svg+xml"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect width='64' height='64' rx='16' fill='%230F172A'/><circle cx='32' cy='32' r='23' stroke='%2338BDF8' stroke-width='2.5' fill='none'/><rect x='22' y='28' width='5' height='10' rx='2' fill='%2338BDF8'/><rect x='30' y='21' width='5' height='17' rx='2' fill='%2338BDF8'/><rect x='38' y='14' width='5' height='24' rx='2' fill='%2338BDF8'/><path d='M21 28l9 9 16-19' stroke='%23FFFFFF' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/></svg>">
    @endif

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

        /**
         * Komponen Dropdown dengan Pencarian (Searchable Select) Otomatis
         * Mengubah seluruh elemen <select> (kecuali yang memiliki atribut data-no-search="true")
         * menjadi dropdown interaktif yang dilengkapi kolom pencarian instan & navigasi keyboard.
         */
        function initChronoSearchableSelects() {
            const selects = document.querySelectorAll('select:not([data-no-search="true"]):not([data-searchable-initialized="true"])');

            selects.forEach((select, index) => {
                select.setAttribute('data-searchable-initialized', 'true');

                // Buat pembungkus utama
                const wrapper = document.createElement('div');
                wrapper.className = 'relative w-full chrono-searchable-select';
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);

                // Sembunyikan select asli secara visual namun tetap aktif untuk form submit & validasi
                select.className = 'sr-only';
                select.tabIndex = -1;

                // Tombol Pemicu (Combobox Trigger)
                const triggerBtn = document.createElement('button');
                triggerBtn.type = 'button';
                triggerBtn.setAttribute('role', 'combobox');
                triggerBtn.setAttribute('aria-expanded', 'false');
                triggerBtn.setAttribute('aria-haspopup', 'listbox');
                triggerBtn.className = 'w-full h-[42px] px-3.5 bg-slate-50 hover:bg-white border border-slate-300 hover:border-slate-400 rounded-xl text-slate-900 text-xs font-semibold flex items-center justify-between gap-2 text-left focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-600 focus:border-sky-600 transition cursor-pointer';

                const triggerText = document.createElement('span');
                triggerText.className = 'truncate block flex-1 text-slate-900';

                const triggerIcon = document.createElement('span');
                triggerIcon.className = 'text-slate-400 shrink-0 transition-transform duration-150';
                triggerIcon.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;

                triggerBtn.appendChild(triggerText);
                triggerBtn.appendChild(triggerIcon);
                wrapper.appendChild(triggerBtn);

                // Panel Popover Dropdown
                const dropdownPanel = document.createElement('div');
                dropdownPanel.className = 'hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white rounded-xl border border-slate-200 shadow-xl overflow-hidden min-w-[200px]';
                dropdownPanel.setAttribute('role', 'listbox');

                // Kotak Input Pencarian di Dalam Dropdown
                const searchContainer = document.createElement('div');
                searchContainer.className = 'p-2 border-b border-slate-100 bg-slate-50/70 sticky top-0';

                const searchInputWrapper = document.createElement('div');
                searchInputWrapper.className = 'relative flex items-center';

                const searchIcon = document.createElement('span');
                searchIcon.className = 'absolute left-2.5 text-slate-400 pointer-events-none';
                searchIcon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>`;

                const searchInput = document.createElement('input');
                searchInput.type = 'text';
                searchInput.placeholder = 'Cari pilihan...';
                searchInput.autocomplete = 'off';
                searchInput.className = 'w-full h-8 pl-8 pr-7 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-sky-600 focus:ring-1 focus:ring-sky-600';

                const clearSearchBtn = document.createElement('button');
                clearSearchBtn.type = 'button';
                clearSearchBtn.title = 'Bersihkan pencarian';
                clearSearchBtn.className = 'hidden absolute right-2 text-slate-400 hover:text-slate-700 cursor-pointer';
                clearSearchBtn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;

                searchInputWrapper.appendChild(searchIcon);
                searchInputWrapper.appendChild(searchInput);
                searchInputWrapper.appendChild(clearSearchBtn);
                searchContainer.appendChild(searchInputWrapper);

                // Daftar Opsi
                const optionsList = document.createElement('div');
                optionsList.className = 'max-h-56 overflow-y-auto py-1 divide-y divide-slate-50';

                dropdownPanel.appendChild(searchContainer);
                dropdownPanel.appendChild(optionsList);
                wrapper.appendChild(dropdownPanel);

                let activeOptionIndex = -1;
                let visibleOptionElements = [];

                function updateTriggerLabel() {
                    const selectedOption = select.options[select.selectedIndex];
                    triggerText.textContent = selectedOption ? selectedOption.textContent.trim() : 'Pilih...';
                }

                function renderOptions(filterQuery = '') {
                    optionsList.innerHTML = '';
                    visibleOptionElements = [];
                    activeOptionIndex = -1;

                    const normalizedQuery = filterQuery.trim().toLowerCase();
                    clearSearchBtn.classList.toggle('hidden', normalizedQuery.length === 0);

                    Array.from(select.options).forEach((opt, optIdx) => {
                        const text = opt.textContent.trim();
                        if (normalizedQuery && !text.toLowerCase().includes(normalizedQuery)) {
                            return;
                        }

                        const isSelected = select.selectedIndex === optIdx;
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.setAttribute('role', 'option');
                        item.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                        item.dataset.optionIndex = String(optIdx);
                        item.className = `w-full px-3 py-2 text-left text-xs flex items-center justify-between gap-2 transition cursor-pointer ${
                            isSelected
                                ? 'bg-slate-100 text-slate-900 font-bold'
                                : 'text-slate-700 hover:bg-slate-50 font-medium'
                        }`;

                        const itemLabel = document.createElement('span');
                        itemLabel.className = 'truncate';
                        itemLabel.textContent = text;

                        item.appendChild(itemLabel);

                        if (isSelected) {
                            const checkMark = document.createElement('span');
                            checkMark.className = 'text-slate-900 font-bold shrink-0 text-xs';
                            checkMark.textContent = '✓';
                            item.appendChild(checkMark);
                        }

                        item.addEventListener('click', (e) => {
                            e.stopPropagation();
                            selectOptionByIndex(optIdx);
                        });

                        optionsList.appendChild(item);
                        visibleOptionElements.push(item);

                        if (isSelected && activeOptionIndex === -1) {
                            activeOptionIndex = visibleOptionElements.length - 1;
                        }
                    });

                    if (visibleOptionElements.length === 0) {
                        const emptyState = document.createElement('div');
                        emptyState.className = 'px-3 py-4 text-center text-xs text-slate-400 italic';
                        emptyState.textContent = 'Data tidak ditemukan';
                        optionsList.appendChild(emptyState);
                    }
                }

                function highlightActiveOption() {
                    visibleOptionElements.forEach((el, idx) => {
                        if (idx === activeOptionIndex) {
                            el.classList.add('bg-slate-100', 'text-slate-900');
                            el.scrollIntoView({ block: 'nearest' });
                        } else if (el.getAttribute('aria-selected') !== 'true') {
                            el.classList.remove('bg-slate-100', 'text-slate-900');
                        }
                    });
                }

                function selectOptionByIndex(optIdx) {
                    select.selectedIndex = optIdx;
                    updateTriggerLabel();
                    closeDropdown(true);
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function openDropdown() {
                    // Tutup semua dropdown lain yang sedang terbuka
                    document.querySelectorAll('.chrono-searchable-select').forEach(otherWrapper => {
                        if (otherWrapper !== wrapper && otherWrapper._closeDropdown) {
                            otherWrapper._closeDropdown(false);
                        }
                    });

                    dropdownPanel.classList.remove('hidden');
                    triggerBtn.setAttribute('aria-expanded', 'true');
                    triggerIcon.style.transform = 'rotate(180deg)';
                    searchInput.value = '';
                    renderOptions('');
                    setTimeout(() => searchInput.focus(), 10);
                }

                function closeDropdown(restoreFocus = false) {
                    dropdownPanel.classList.add('hidden');
                    triggerBtn.setAttribute('aria-expanded', 'false');
                    triggerIcon.style.transform = 'rotate(0deg)';
                    if (restoreFocus) {
                        triggerBtn.focus();
                    }
                }

                wrapper._closeDropdown = closeDropdown;

                triggerBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = !dropdownPanel.classList.contains('hidden');
                    if (isOpen) {
                        closeDropdown(false);
                    } else {
                        openDropdown();
                    }
                });

                triggerBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openDropdown();
                    }
                });

                searchInput.addEventListener('input', () => {
                    renderOptions(searchInput.value);
                    if (visibleOptionElements.length > 0) {
                        activeOptionIndex = 0;
                        highlightActiveOption();
                    }
                });

                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (visibleOptionElements.length > 0) {
                            activeOptionIndex = (activeOptionIndex + 1) % visibleOptionElements.length;
                            highlightActiveOption();
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (visibleOptionElements.length > 0) {
                            activeOptionIndex = (activeOptionIndex - 1 + visibleOptionElements.length) % visibleOptionElements.length;
                            highlightActiveOption();
                        }
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (activeOptionIndex >= 0 && visibleOptionElements[activeOptionIndex]) {
                            const optIdx = parseInt(visibleOptionElements[activeOptionIndex].dataset.optionIndex, 10);
                            selectOptionByIndex(optIdx);
                        }
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        e.stopPropagation();
                        closeDropdown(true);
                    }
                });

                clearSearchBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    searchInput.value = '';
                    renderOptions('');
                    searchInput.focus();
                });

                // Hubungkan klik pada <label for="..."> agar membuka dropdown ini
                if (select.id) {
                    const associatedLabel = document.querySelector(`label[for="${CSS.escape(select.id)}"]`);
                    if (associatedLabel) {
                        associatedLabel.addEventListener('click', (e) => {
                            e.preventDefault();
                            openDropdown();
                        });
                    }
                }

                // Sinkronisasi jika nilai select diubah secara eksternal lewat JS
                select.addEventListener('change', () => {
                    updateTriggerLabel();
                });

                updateTriggerLabel();
            });
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.chrono-searchable-select').forEach(wrapper => {
                if (!wrapper.contains(e.target) && wrapper._closeDropdown) {
                    wrapper._closeDropdown(false);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', initChronoSearchableSelects);
    </script>
</body>
</html>
