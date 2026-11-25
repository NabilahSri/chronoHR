@if (isset($paginator) && $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
    @php
        $currentPerPage = (int) request('per_page', $paginator->perPage());
        $perPageOptions = [5, 10, 15, 25, 50];
        if (!in_array($currentPerPage, $perPageOptions)) {
            $perPageOptions[] = $currentPerPage;
            sort($perPageOptions);
        }
        $currentPage = $paginator->currentPage();
        $lastPage = max(1, $paginator->lastPage());
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
    @endphp

    <div class="px-5 py-3.5 sm:px-6 border-t border-slate-200/80 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs print:hidden">
        <!-- Info Jumlah Baris & Pilihan Per Halaman -->
        <div class="flex flex-wrap items-center gap-3 text-slate-600">
            <div>
                @if ($paginator->total() > 0)
                    Menampilkan <strong class="font-mono font-bold text-slate-900">{{ $paginator->firstItem() }}</strong>
                    sampai <strong class="font-mono font-bold text-slate-900">{{ $paginator->lastItem() }}</strong>
                    dari <strong class="font-mono font-bold text-slate-900">{{ $paginator->total() }}</strong> data
                @else
                    Menampilkan <strong class="font-mono font-bold text-slate-900">0</strong> data
                @endif
            </div>

            <div class="flex items-center gap-1.5 sm:border-l sm:border-slate-200 sm:pl-3">
                <label for="per_page_{{ $paginator->getPageName() }}" class="text-slate-500 font-medium">Baris:</label>
                <select id="per_page_{{ $paginator->getPageName() }}"
                    data-no-search="true"
                    onchange="const url = new URL(window.location.href); url.searchParams.set('per_page', this.value); url.searchParams.delete('{{ $paginator->getPageName() }}'); window.location.href = url.toString();"
                    class="h-7 px-2 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 cursor-pointer">
                    @foreach ($perPageOptions as $opt)
                        <option value="{{ $opt }}" {{ $currentPerPage === $opt ? 'selected' : '' }}>
                            {{ $opt }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Navigasi Nomor Halaman -->
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center justify-end gap-1">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="h-8 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-400 font-semibold cursor-not-allowed select-none">
                    &lsaquo; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="h-8 px-3 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-semibold transition">
                    &lsaquo; Sebelumnya
                </a>
            @endif

            {{-- Halaman Pertama jika jauh --}}
            @if ($startPage > 1)
                <a href="{{ $paginator->url(1) }}"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-mono font-bold transition">
                    1
                </a>
                @if ($startPage > 2)
                    <span class="px-1 text-slate-400 font-mono">...</span>
                @endif
            @endif

            {{-- Rentang Nomor Halaman Aktif --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <span aria-current="page"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-900 bg-slate-900 text-white font-mono font-bold shadow-2xs">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-mono font-bold transition">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Halaman Terakhir jika jauh --}}
            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="px-1 text-slate-400 font-mono">...</span>
                @endif
                <a href="{{ $paginator->url($lastPage) }}"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-mono font-bold transition">
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="h-8 px-3 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 font-semibold transition">
                    Berikutnya &rsaquo;
                </a>
            @else
                <span class="h-8 px-3 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100 text-slate-400 font-semibold cursor-not-allowed select-none">
                    Berikutnya &rsaquo;
                </span>
            @endif
        </nav>
    </div>
@endif
