<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <form action="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Cari produk berdasarkan nama atau barcode..." 
                               class="w-full border-gray-300 rounded-lg shadow-sm pl-10 focus:border-sky-500 focus:ring-sky-500">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        @if($search)
                            <a href="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" class="absolute right-3 top-2.5 text-gray-500 hover:text-red-500" title="Reset Pencarian">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-3 p-3 h-[65vh] overflow-y-auto custom-scrollbar content-start">
                    @forelse ($produks as $produk)
                        <form action="{{ route('pos.add_item') }}" method="POST" class="h-full">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                            <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                            
                            <button type="submit" 
                                    class="group w-full text-left bg-white border border-gray-200 rounded-xl hover:shadow-md hover:border-sky-500 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-sky-500 relative overflow-hidden h-full flex flex-col">
                                
                                <div class="h-20 w-full bg-gray-50 flex items-center justify-center group-hover:bg-sky-50 transition-colors relative">
                                    <svg class="w-8 h-8 text-gray-300 group-hover:text-sky-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    
                                    <span class="absolute top-1 right-1 text-[10px] font-semibold text-gray-500 bg-white/90 px-1.5 py-0.5 rounded shadow-sm border border-gray-100">
                                        {{ $produk->kategori->nama ?? 'Umum' }}
                                    </span>
                                </div>
                                
                                <div class="p-3 flex flex-col justify-between flex-grow">
                                    <div>
                                        <h3 class="text-sm font-bold text-gray-800 leading-tight line-clamp-2 group-hover:text-sky-600 transition-colors">
                                            {{ $produk->nama_produk }}
                                        </h3>
                                        <p class="text-[10px] text-gray-400 mt-1">Stok: {{ $produk->stok }}</p>
                                    </div>
                                    
                                    <div class="mt-2 flex items-end justify-between border-t border-dashed border-gray-200 pt-2">
                                        <span class="text-sm font-extrabold text-gray-900">
                                            Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                        </span>
                                        <div class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center group-hover:bg-sky-500 group-hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </form>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20">
                            <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p>Produk tidak ditemukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow space-y-6 sticky top-24 h-[85vh] flex flex-col border border-gray-100">
                
                <div class="flex justify-between items-center border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">Keranjang</h2>
                    <span class="text-xs font-mono font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">#{{ $activeDraft->id }}</span>
                </div>

                @if($pendingDrafts->isNotEmpty())
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full flex justify-between items-center px-3 py-2 bg-yellow-50 text-yellow-700 text-sm font-medium rounded-lg hover:bg-yellow-100 transition-colors border border-yellow-200">
                            <span>⚠️ {{ $pendingDrafts->count() }} Transaksi Ditahan</span>
                            <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-20 mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-100 max-h-48 overflow-y-auto">
                            @foreach ($pendingDrafts as $draft)
                                <a href="{{ route('pos.index', ['transaksi' => $draft->id]) }}" class="block px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-xs text-gray-800">{{ $draft->pelanggan->nama ?? 'Umum' }}</span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($draft->tanggal)->format('H:i') }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">Total: Rp {{ number_format($draft->details->sum('subtotal'), 0, ',', '.') }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div x-data="{ mode: 'select' }" class="bg-gray-50 p-3 rounded-lg border border-gray-200 transition-all">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Pelanggan</label>
                        <button type="button" 
                                @click="mode = (mode === 'select' ? 'new' : 'select')" 
                                class="text-xs text-sky-600 hover:text-sky-800 font-semibold focus:outline-none flex items-center gap-1 transition-colors">
                            <span x-text="mode === 'select' ? '+ Ketik Nama Baru' : 'Kembali ke List'"></span>
                        </button>
                    </div>

                    <form action="{{ route('pos.save_customer') }}" method="POST">
                        @csrf
                        <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                        
                        <div x-show="mode === 'select'" x-transition>
                            <select name="pelanggan_id" 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2"
                                    onchange="this.form.submit()">
                                <option value="">-- Pelanggan Umum --</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}" {{ $activeDraft->pelanggan_id == $pelanggan->id ? 'selected' : '' }}>
                                        {{ $pelanggan->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="mode === 'new'" style="display: none;" x-transition>
                            <div class="flex gap-2">
                                <input type="text" name="nama_pelanggan_baru" placeholder="Ketik nama..." 
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2"
                                       x-bind:disabled="mode !== 'new'" autocomplete="off">
                                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors">Set</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    @forelse ($activeDraft->details as $item)
                        <div class="flex justify-between items-start group bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <div class="flex-grow pr-2">
                                <span class="block text-sm font-medium text-gray-800 line-clamp-2">{{ $item->produk->nama_produk ?? 'Item dihapus' }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">@ Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="text-sm font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                <div class="flex items-center space-x-1 bg-white rounded border border-gray-200">
                                    <form action="{{ route('pos.update_item') }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                        <input type="number" name="qty" value="{{ $item->jumlah }}" min="1"
                                               class="w-10 text-center border-none p-0 text-sm h-6 focus:ring-0"
                                               onchange="this.form.submit()">
                                    </form>
                                    <form action="{{ route('pos.remove_item') }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                        <button type="submit" class="px-1.5 py-0.5 text-gray-400 hover:text-red-500 transition-colors border-l">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p class="text-sm">Keranjang Kosong</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t pt-4 space-y-4">
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-sky-600">
                            <span>Total</span>
                            <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('pos.checkout.show', $activeDraft) }}"
                           class="w-full flex items-center justify-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-sky-200 transition duration-200 {{ $activeDraft->details->isEmpty() ? 'opacity-50 pointer-events-none' : '' }}">
                            <span class="mr-2">Bayar Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('pos.new_draft') }}"
                               class="flex items-center justify-center bg-yellow-50 text-yellow-700 hover:bg-yellow-100 font-semibold py-2.5 px-4 rounded-lg border border-yellow-200 transition duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Simpan
                            </a>
                            <button type="button"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-draft-cancel-{{ $activeDraft->id }}')"
                                    class="flex items-center justify-center bg-red-50 text-red-700 hover:bg-red-100 font-semibold py-2.5 px-4 rounded-lg border border-red-200 transition duration-200 text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <x-modal :name="'confirm-draft-cancel-'.$activeDraft->id" focusable>
            <form method="post" action="{{ route('pos.cancel_draft') }}" class="p-6">
                @csrf
                <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                <h2 class="text-lg font-medium text-gray-900">Hapus Transaksi Ini?</h2>
                <p class="mt-1 text-sm text-gray-600">Item di keranjang akan dihapus permanen.</p>
                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-danger-button>Ya, Hapus</x-danger-button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>