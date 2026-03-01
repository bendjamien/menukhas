<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-sky-100 text-sky-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Shift & Selisih Laci</h2>
                        <p class="text-gray-500 text-sm">Pantau akuntabilitas kasir dan rekonsiliasi uang tunai.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kasir</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu Buka/Tutup</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Modal Awal</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ekspektasi (Sistem)</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aktual (Fisik)</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Selisih</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($shifts as $s)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800 text-sm">{{ $s->user->name }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">{{ $s->user->role }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-gray-600">{{ \Carbon\Carbon::parse($s->waktu_buka)->format('d M Y, H:i') }}</div>
                                    <div class="text-[10px] text-gray-400 italic">s/d {{ $s->waktu_tutup ? \Carbon\Carbon::parse($s->waktu_tutup)->format('H:i') : 'Sekarang' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm">Rp {{ number_format($s->saldo_awal, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-mono text-sm text-gray-500">Rp {{ number_format($s->total_tunai_diharapkan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-mono text-sm font-bold text-gray-800">Rp {{ number_format($s->total_tunai_aktual, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($s->status === 'closed')
                                        @if($s->selisih < 0)
                                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-black">
                                                - Rp {{ number_format(abs($s->selisih), 0, ',', '.') }}
                                            </span>
                                        @elseif($s->selisih > 0)
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-black">
                                                + Rp {{ number_format($s->selisih, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 font-bold text-xs uppercase italic tracking-widest">Match</span>
                                        @endif
                                    @else
                                        <span class="text-gray-300 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($s->status === 'open')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-sky-600 uppercase tracking-widest px-2 py-1 bg-sky-50 rounded-lg">
                                            <span class="w-1.5 h-1.5 bg-sky-500 rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 py-1 bg-gray-100 rounded-lg">Closed</span>
                                    @endif
                                </td>
                            </tr>
                            @if($s->catatan)
                            <tr class="bg-gray-50/30">
                                <td colspan="7" class="px-6 py-2 text-[10px] text-gray-400 italic">
                                    <strong>Catatan:</strong> {{ $s->catatan }}
                                </td>
                            </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-bold uppercase tracking-[0.2em]">Belum ada data shift.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($shifts->hasPages())
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $shifts->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
