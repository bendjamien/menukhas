<x-app-layout>
    <div class="space-y-6" x-data="{ 
        deleteUrl: '', 
        itemName: '' 
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold">Kelola data member dan poin pelanggan</p>
            </div>
            <a href="{{ route('pelanggan.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-sky-100 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pelanggan
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('pelanggan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor HP..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:bg-white text-sm transition-all">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <button type="submit" class="px-8 py-2.5 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-xl transition-all shadow-md">Cari</button>
                @if(request('search'))
                    <a href="{{ route('pelanggan.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all text-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Kontak</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Loyalty</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pelanggans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-sm border border-sky-200">
                                            {{ substr($p->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $p->nama }}</div>
                                            <div class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $p->kode_member ?? 'NON-MEMBER' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">{{ $p->no_hp ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $p->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-700 rounded-lg border border-amber-100">
                                            {{ $p->member_level ?? 'Regular' }}
                                        </span>
                                        <div class="text-xs font-bold text-gray-900 mt-1">{{ number_format($p->poin) }} Poin</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('pelanggan.show', $p->id) }}" class="p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c3.477 0 6.517 1.735 8.307 4.387a1.1 1.1 0 010 1.226C18.517 17.265 15.477 19 12 19c-4.477 0-7.523-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('pelanggan.edit', $p->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <button @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteUrl = '{{ route('pelanggan.destroy', $p->id) }}'; itemName = '{{ $p->nama }}'" 
                                                class="p-2 text-rose-400 hover:text-rose-600 transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">Pelanggan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pelanggans->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $pelanggans->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-delete-modal" focusable maxWidth="sm">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Hapus Pelanggan?</h2>
                <p class="text-slate-500 text-sm mb-8">Anda yakin ingin menghapus <span class="font-bold text-slate-800" x-text="itemName"></span>? Riwayat transaksi pelanggan ini tetap akan tersimpan di sistem.</p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-rose-100">Ya, Hapus</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>