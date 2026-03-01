<x-app-layout>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Daftar Pengeluaran</h1>
                <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Monitor Semua Biaya Operasional Toko</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pengeluaran.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 transition-all text-sm shadow-lg shadow-rose-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Pengeluaran
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 text-sm font-medium">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="py-2.5 px-6 bg-slate-800 hover:bg-black text-white font-bold rounded-xl transition-all shadow-md text-sm">Filter</button>
                    <a href="{{ route('pengeluaran.index') }}" class="py-2.5 px-6 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all text-sm">Reset</a>
                    
                    <div class="h-10 w-px bg-slate-200 mx-2 hidden lg:block"></div>
                    
                    <a href="{{ route('pengeluaran.export_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="py-2.5 px-5 bg-rose-50 text-rose-600 font-bold rounded-xl hover:bg-rose-100 transition-all text-sm flex items-center gap-2 border border-rose-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                    
                    <a href="{{ route('pengeluaran.export_excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="py-2.5 px-5 bg-emerald-50 text-emerald-600 font-bold rounded-xl hover:bg-emerald-100 transition-all text-sm flex items-center gap-2 border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Card -->
        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center gap-4 mb-4 md:mb-0">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-rose-500 uppercase tracking-widest">Total Pengeluaran Periode Ini</span>
                    <p class="text-2xl font-black text-rose-900 leading-tight">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Info</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dicatat Oleh</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($pengeluarans as $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-slate-700 text-sm">#EXP-{{ $p->id }}</span>
                                    <span class="text-xs text-slate-400 font-medium">{{ $p->tanggal->format('d M Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ $p->kategori }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 font-medium">{{ $p->keterangan }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-slate-500 font-bold italic">{{ $p->user->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-rose-600">Rp {{ number_format($p->nominal, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('pengeluaran.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada catatan pengeluaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pengeluarans->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $pengeluarans->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>