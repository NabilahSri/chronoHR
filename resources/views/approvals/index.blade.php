@extends('layouts.app')

@section('title', 'Persetujuan Cuti & Izin — ChronoHR ')
@section('page_title', 'Otoritas Persetujuan Cuti, Izin & WFH')

@section('content')
    <div class="space-y-8">

        <!-- Pending Requests Section -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>Menunggu Persetujuan Atasan</span>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            {{ $pendingLeaves->count() }} Antrean
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pengajuan yang disetujui akan langsung menggenerasi data pada
                        tabel presensi secara otomatis.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($pendingLeaves as $leave)
                    <div
                        class="bg-white/95 rounded-3xl border border-slate-200/90 p-6 shadow-xs backdrop-blur-xs flex flex-col justify-between hover:shadow-sm transition-all">
                        <div>
                            <!-- Header Card -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($leave->user->name ?? 'K', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">{{ $leave->user->name ?? '—' }}</h3>
                                        <p class="text-xs text-slate-400 font-mono">{{ $leave->user->email ?? '' }}</p>
                                    </div>
                                </div>

                                <span
                                    class="px-3 py-1 rounded-full text-xs font-mono font-extrabold {{ $leave->type === 'WFH' ? 'bg-sky-50 text-sky-700 border border-sky-200' : ($leave->type === 'SAKIT' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    {{ $leave->type }}
                                </span>
                            </div>

                            <!-- Date & Reason Box -->
                            <div class="bg-slate-50/70 rounded-2xl p-4 text-xs space-y-2 mb-5 border border-slate-200/80">
                                <div class="flex items-center gap-2 text-slate-800 font-medium font-mono">
                                    <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-bold text-slate-900">{{ $leave->start_date }}</span>
                                    <span class="text-slate-400 font-sans">sampai</span>
                                    <span class="font-bold text-slate-900">{{ $leave->end_date }}</span>
                                </div>
                                <div class="pt-1.5 border-t border-slate-200/60 text-slate-600 font-sans leading-relaxed">
                                    <span
                                        class="font-semibold text-slate-700 block text-[11px] uppercase tracking-wider mb-0.5">Alasan
                                        Pengajuan:</span>
                                    "{{ $leave->reason }}"
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                            <!-- Approve Form -->
                            <form action="{{ route('approval.approve', $leave) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.99] text-white rounded-xl text-xs font-semibold shadow-xs hover:shadow transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Setujui Pengajuan</span>
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form action="{{ route('approval.reject', $leave) }}" method="POST" class="flex-1"
                                onsubmit="var reason = prompt('Masukkan alasan penolakan:'); if(!reason) return false; this.rejection_reason.value = reason; return true;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="rejection_reason">
                                <button type="submit"
                                    class="w-full py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-semibold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>Tolak</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full bg-white/95 rounded-3xl border border-slate-200/90 p-12 text-center text-slate-400 backdrop-blur-xs">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="font-bold text-slate-700">Semua Pengajuan Telah Ditinjau</p>
                        <p class="text-xs text-slate-400 mt-1">Tidak ada permohonan cuti, izin, atau WFH yang sedang
                            tertunda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- History Requests Section -->
        <div class="bg-white/95 rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden backdrop-blur-xs">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Riwayat Keputusan Pengajuan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan seluruh permohonan yang telah disetujui atau ditolak.
                    </p>
                </div>
                <span
                    class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    Terbaru (20 Data)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50/80 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Karyawan</th>
                            <th class="px-6 py-4">Tipe Izin</th>
                            <th class="px-6 py-4">Rentang Tanggal</th>
                            <th class="px-6 py-4">Status Keputusan</th>
                            <th class="px-6 py-4">Ditinjau Oleh</th>
                            <th class="px-6 py-4">Keterangan / Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($historyLeaves as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $item->user->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $item->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold">
                                    <span
                                        class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono font-semibold text-slate-800">
                                    {{ $item->start_date }} <span class="text-slate-400 font-normal">s/d</span>
                                    {{ $item->end_date }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->status === 'APPROVED')
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            APPROVED
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                            <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            REJECTED
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    {{ $item->approver->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-500 max-w-xs truncate">
                                    {{ $item->status === 'REJECTED' ? 'Alasan Tolak: ' . $item->rejection_reason : $item->reason }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                    <p class="font-medium text-slate-600">Belum ada riwayat permohonan yang diproses</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
