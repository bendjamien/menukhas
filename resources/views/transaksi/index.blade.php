<x-app-layout>
    <div class="space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Riwayat Transaksi</h1>
                <p class="text-slate-500 text-sm mt-1">Pantau semua aktivitas penjualan toko Anda.</p>
            </div>
            
            <!-- Quick Stats (Optional, adds professional feel) -->
            <div class="hidden md:flex gap-6">
                <div class="text-right">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                    <p class="text-xl font-bold text-slate-700">{{ $transaksis->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('transaksi.index') }}" method="GET">
                <div class="flex flex-col lg:flex-row gap-4 items-end">
                    
                    <div class="w-full lg:flex-1">
                        <label for="search" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Pencarian
                        </label>
                        <div class="relative">
                            <input type="search" name="search" id="search" value="{{ $search ?? '' }}" 
                                   placeholder="Cari ID, nama kasir, atau pelanggan..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all text-sm font-medium placeholder-slate-400">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <div class="w-full lg:w-auto flex gap-4">
                        <div>
                            <label for="start_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dari</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                                   class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-medium text-slate-600">
                        </div>
                        <div>
                            <label for="end_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                                   class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm font-medium text-slate-600">
                        </div>
                    </div>
                    
                    <div class="flex gap-2 w-full lg:w-auto">
                        <button type="submit" 
                                class="flex-1 lg:flex-none py-2.5 px-6 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-sky-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                        </button>
                        <a href="{{ route('transaksi.index') }}" 
                           class="py-2.5 px-4 bg-slate-100 text-slate-500 font-bold rounded-xl hover:bg-slate-200 transition-colors flex items-center justify-center" title="Reset">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transactions List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Transaksi Info</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Kasir & Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total Bayar</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($transaksis as $transaksi)
                            <tr class="hover:bg-sky-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-xs">
                                            #{{ $transaksi->id }}
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-700">Order #{{ $transaksi->id }}</span>
                                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span class="text-sm font-medium text-slate-700">{{ $transaksi->kasir->name ?? 'Unknown' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            <span class="text-xs text-slate-500">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                        {{ $transaksi->metode_bayar == 'Tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $transaksi->metode_bayar }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="block text-sm font-black text-slate-800">
                                        Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('transaksi.show', $transaksi) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-sky-500 hover:text-white transition-all shadow-sm" 
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                        <p class="text-lg font-medium text-slate-600">Belum ada transaksi.</p>
                                        <p class="text-sm">Coba sesuaikan filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Responsive) -->
            <div class="md:hidden space-y-4 p-4 bg-slate-50">
                @forelse ($transaksis as $transaksi)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 px-3 py-1 bg-slate-100 rounded-bl-xl text-[10px] font-bold text-slate-500">
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M, H:i') }}
                        </div>
                        
                        <div class="flex justify-between items-start mb-3 mt-1">
                            <div>
                                <span class="text-xs font-bold text-sky-600 block mb-1">Order #{{ $transaksi->id }}</span>
                                <h3 class="text-lg font-black text-slate-800">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-500 mb-4 border-t border-b border-dashed border-slate-100 py-3">
                            <div>
                                <span class="block text-[10px] uppercase tracking-wider text-slate-400">Kasir</span>
                                <span class="font-medium text-slate-700">{{ $transaksi->kasir->name ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-[10px] uppercase tracking-wider text-slate-400">Pelanggan</span>
                                <span class="font-medium text-slate-700">{{ $transaksi->pelanggan->nama ?? 'Umum' }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold 
                                {{ $transaksi->metode_bayar == 'Tunai' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $transaksi->metode_bayar }}
                            </span>
                            <a href="{{ route('transaksi.show', $transaksi) }}" class="text-sm font-bold text-sky-600 hover:text-sky-800 flex items-center gap-1">
                                Lihat Detail &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400">
                        <p>Tidak ada data.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($transaksis->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>