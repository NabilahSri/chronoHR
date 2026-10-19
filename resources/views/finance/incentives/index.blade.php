@extends('layouts.app')

@section('title', 'Insentif Magang — ChronoHR ')
@section('page_title', 'Insentif Proyek Personil Magang (INTERN)')

@section('content')
    <div class="space-y-8">

        <!-- Form Pencatatan Insentif Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs backdrop-blur-xs w-full">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Catat Insentif Proyek Magang Baru</h2>
                <p class="text-xs text-slate-500 mt-1">Insentif ini akan langsung diakumulasikan ke dalam nominal gaji netto
                    bulanan anak magang.</p>
            </div>

            <form action="{{ route('finance.incentives.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Personil Magang
                        (INTERN)</label>
                    <select name="user_id" required
                        class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                        <option value="">— Pilih Mahasiswa / Siswa Magang —</option>
                        @foreach ($interns as $intern)
                            <option value="{{ $intern->id }}">{{ $intern->name }} ({{ $intern->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Proyek /
                            Fitur</label>
                        <input type="text" name="project_name" placeholder="Contoh: Redesign UI Mobile Flutter" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nominal Insentif
                            (IDR)</label>
                        <input type="number" name="amount" min="1000" placeholder="Contoh: 1500000" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm font-mono focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal
                            Penyerahan</label>
                        <input type="date" name="given_date" value="{{ now()->toDateString() }}" required
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 text-sm font-mono focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Kinerja
                            (Opsional)</label>
                        <input type="text" name="notes" placeholder="Catatan milestone..."
                            class="w-full px-4 py-2.5 bg-slate-50/70 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all">
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100">
                    <button type="submit"
                        class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold shadow-sm hover:shadow active:scale-[0.99] transition-all cursor-pointer">
                        Simpan Insentif Magang
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Insentif Card -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Riwayat Catatan Insentif</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Seluruh insentif proyek yang telah diserahkan kepada personil
                        magang.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/80 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Nama Intern</th>
                            <th class="px-6 py-4">Nama Proyek</th>
                            <th class="px-6 py-4">Tanggal Penyerahan</th>
                            <th class="px-6 py-4">Nominal Insentif</th>
                            <th class="px-6 py-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($incentives as $inc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $inc->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $inc->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $inc->project_name }}</td>
                                <td class="px-6 py-4 font-mono text-xs">{{ $inc->given_date }}</td>
                                <td class="px-6 py-4 font-mono text-xs font-extrabold text-emerald-600">
                                    Rp {{ number_format($inc->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">{{ $inc->notes ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                    <p class="font-medium text-slate-600">Belum ada data insentif proyek yang dicatat</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($incentives->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $incentives->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
