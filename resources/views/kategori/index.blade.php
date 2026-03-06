<x-app-layout>
    <div class="space-y-6" x-data="{ 
        deleteUrl: '', 
        itemName: '' 
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Kategori Produk</h1>
                <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold">Kelola pengelompokan menu/produk Anda</p>
            </div>
            <a href="{{ route('kategori.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-sky-100 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kategori
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Total Produk</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kategoris as $k)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $k->nama }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest bg-sky-50 text-sky-700 rounded-lg">
                                    {{ $k->produks_count }} Produk
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('kategori.edit', $k->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteUrl = '{{ route('kategori.destroy', $k->id) }}'; itemName = '{{ $k->nama }}'" 
                                            class="p-2 text-rose-400 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">Kategori belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-delete-modal" focusable maxWidth="sm">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Hapus Kategori?</h2>
                <p class="text-slate-500 text-sm mb-8">Menghapus kategori <span class="font-bold text-slate-800" x-text="itemName"></span> mungkin akan mempengaruhi pengelompokan produk Anda.</p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-rose-100">Ya, Hapus</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>