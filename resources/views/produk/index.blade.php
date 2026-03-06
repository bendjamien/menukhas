<x-app-layout>
    <div class="space-y-6" x-data="{ 
        deleteUrl: '', 
        itemName: '' 
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Produk</h1>
                <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold">Kelola menu dan inventaris toko Anda</p>
            </div>
            <a href="{{ route('produk.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-sky-100 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Produk
            </a>
        </div>

        <!-- Filter & Search -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('produk.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau kode barcode..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:bg-white text-sm transition-all">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="kategori" class="w-full py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-md">Filter</button>
                @if(request()->anyFilled(['search', 'kategori']))
                    <a href="{{ route('produk.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all text-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Harga Jual</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Stok</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($produks as $produk)
                            @php
                                $stokColor = $produk->stok <= 5 ? 'text-red-700 bg-red-50 border-red-100' : 'text-emerald-700 bg-emerald-50 border-emerald-100';
                                $barColor = $produk->stok <= 5 ? 'bg-red-500' : 'bg-emerald-500';
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden border border-gray-100">
                                            @if($produk->image)
                                                <img src="{{ asset('storage/' . $produk->image) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $produk->nama_produk }}</div>
                                            <div class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $produk->barcode ?? 'No Barcode' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 rounded-lg">
                                        {{ $produk->kategori->nama ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-black text-gray-900">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-gray-400">Modal: Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $stokColor }}">
                                            {{ $produk->stok }} {{ $produk->satuan }}
                                        </span>
                                        <div class="w-20 bg-gray-100 rounded-full h-1 mt-2 overflow-hidden">
                                            <div class="{{ $barColor }} h-1 rounded-full" style="width: {{ min(($produk->stok / 50) * 100, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('produk.edit', $produk->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <button @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteUrl = '{{ route('produk.destroy', $produk->id) }}'; itemName = '{{ $produk->nama_produk }}'" 
                                                class="p-2 text-rose-400 hover:text-rose-600 transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">Produk tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($produks->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $produks->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-delete-modal" focusable maxWidth="sm">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Hapus Produk?</h2>
                <p class="text-slate-500 text-sm mb-8">Anda yakin ingin menghapus <span class="font-bold text-slate-800" x-text="itemName"></span>? Riwayat stok produk ini juga akan terpengaruh.</p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-rose-100">Ya, Hapus</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>