<x-app-layout>
    <div class="space-y-8" x-data="{ actionUrl: '', orderId: '', modalTitle: '', modalMessage: '', confirmBtnClass: '' }">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Riwayat Hapus Transaksi</h1>
                <p class="text-rose-600 text-sm mt-1 font-bold uppercase tracking-widest">Data Transaksi yang Telah Dihapus</p>
            </div>
            
            <a href="{{ route('transaksi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all text-xs shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Riwayat
            </a>
        </div>

        <!-- Search Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('transaksi.trashed') }}" method="GET">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="search" name="search" value="{{ $search ?? '' }}" 
                                   placeholder="Cari ID atau pelanggan..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 transition-all text-sm font-medium">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <button type="submit" class="py-2.5 px-6 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-rose-200 text-sm">Cari</button>
                </div>
            </form>
        </div>

        <!-- Trashed List -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Transaksi Info</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Dihapus Pada</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($transaksis as $transaksi)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-slate-700">Order #{{ $transaksi->id }}</span>
                                    <span class="text-xs text-slate-400">{{ $transaksi->pelanggan->nama ?? 'Umum' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-600">{{ $transaksi->deleted_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-slate-800 text-sm">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="actionUrl = '{{ route('transaksi.restore', $transaksi->id) }}'; 
                                                        orderId = '#{{ $transaksi->id }}';
                                                        modalTitle = 'Pulihkan Transaksi?';
                                                        modalMessage = 'Transaksi ini akan dikembalikan ke riwayat utama.';
                                                        confirmBtnClass = 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-100';
                                                        $dispatch('open-modal', 'action-modal')"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Pulihkan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                        <button @click="actionUrl = '{{ route('transaksi.force_delete', $transaksi->id) }}'; 
                                                        orderId = '#{{ $transaksi->id }}';
                                                        modalTitle = 'Hapus Permanen?';
                                                        modalMessage = 'Data ini akan dihapus selamanya dari database dan tidak bisa dipulihkan.';
                                                        confirmBtnClass = 'bg-rose-600 hover:bg-rose-700 shadow-rose-100';
                                                        $dispatch('open-modal', 'action-modal')"
                                                class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Permanen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada riwayat hapus.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transaksis->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>

        <!-- Action Confirmation Modal -->
        <x-modal name="action-modal" focusable maxWidth="sm">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight" x-text="modalTitle"></h2>
                <p class="text-slate-500 text-sm mb-8">Transaksi <span class="font-bold text-slate-800" x-text="orderId"></span>. <span x-text="modalMessage"></span></p>
                
                <form :action="actionUrl" method="POST" class="flex gap-3">
                    @csrf
                    <template x-if="modalTitle === 'Hapus Permanen?'">
                        <input type="hidden" name="_method" value="DELETE">
                    </template>
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 text-white font-black rounded-xl uppercase tracking-widest text-[10px] transition-all" :class="confirmBtnClass">Konfirmasi</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>
