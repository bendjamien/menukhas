<x-app-layout>
    {{-- Main Container --}}
    <div class="h-[calc(100vh-100px)] mt-4 mx-4 flex flex-col overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-lg">
        
        <div class="flex flex-col md:flex-row flex-grow overflow-hidden">
            
            <!-- LEFT SIDE: PRODUCTS -->
            <div class="flex-grow flex flex-col min-w-0 border-r border-gray-100 bg-gray-50/30">
                
                <!-- ACTION BAR -->
                <div class="p-4 bg-white border-b border-gray-100 shrink-0">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <form action="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" method="GET" class="relative">
                                <input type="text" name="search" id="pos-search-input" value="{{ $search ?? '' }}" 
                                       placeholder="Cari menu atau scan barcode..." 
                                       class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-10 py-3 focus:ring-2 focus:ring-sky-500 focus:bg-white font-medium text-sm transition-all"
                                       autofocus autocomplete="off">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                @if($search)
                                    <a href="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </form>
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'scan-barcode-modal'); startBarcodeScanner()"
                                    class="bg-sky-600 hover:bg-sky-700 text-white px-5 rounded-xl shadow-md transition-all flex items-center gap-2 group shrink-0">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                <span class="font-bold text-xs uppercase tracking-widest hidden lg:inline">Scan</span>
                            </button>

                            @if(Auth::user()->role === 'kasir')
                            <a href="{{ route('shift.close.index') }}" 
                               class="bg-gray-900 hover:bg-black text-white px-5 rounded-xl shadow-md transition-all flex items-center gap-2 shrink-0 border-2 border-gray-800">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                <span class="font-bold text-xs uppercase tracking-widest">Tutup Shift</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-4 bg-gray-50/20">
                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @forelse ($produks as $produk)
                            @php
                                $qtyInCart = $activeDraft->details->where('produk_id', $produk->id)->sum('jumlah') ?? 0;
                                $displayStock = $produk->stok - $qtyInCart;
                            @endphp
                            <div x-data="{ qty: 1, currentStock: {{ $displayStock }} }" 
                                 class="product-card" 
                                 data-produk-id="{{ $produk->id }}" 
                                 data-stok-asli="{{ $produk->stok }}"
                                 x-on:stock-updated-{{ $produk->id }}.window="currentStock = $event.detail.available">
                                
                                <div class="bg-white rounded-2xl border border-gray-200 hover:border-sky-400 hover:shadow-lg transition-all duration-300 flex flex-col h-full overflow-hidden shadow-sm group relative">
                                    
                                    <!-- Area Gambar -->
                                    <div @click="if(currentStock > 0) addItemAJAX($refs.addForm)" class="aspect-square bg-gray-50 flex items-center justify-center cursor-pointer relative overflow-hidden border-b border-gray-100">
                                        @if($produk->image)
                                            <img src="{{ asset('storage/' . $produk->image) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-gray-200 group-hover:text-sky-200 transition-colors">
                                                <svg class="w-12 h-12 group-hover:scale-110 transition-all duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                        
                                        <div class="absolute top-2 left-2">
                                            <span :class="currentStock <= 5 ? 'bg-rose-500' : 'bg-sky-600'" class="text-[8px] font-black text-white px-2 py-0.5 rounded-full shadow-lg uppercase" x-text="'STOK: ' + currentStock"></span>
                                        </div>
                                    </div>

                                    <!-- Content Area -->
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h3 class="text-xs font-black text-gray-800 line-clamp-1 mb-1 group-hover:text-sky-600 transition-colors uppercase tracking-tight" title="{{ $produk->nama_produk }}">
                                            {{ $produk->nama_produk }}
                                        </h3>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-4">{{ $produk->kategori->nama ?? 'Umum' }}</div>
                                        
                                        <div class="mt-auto flex items-center justify-between gap-3 border-t border-gray-50 pt-3 pr-1">
                                            <div class="text-sm font-black text-gray-900 tracking-tighter leading-none">
                                                <span class="text-sky-600 text-[10px] font-bold mr-0.5">Rp</span>{{ number_format($produk->harga_jual, 0, ',', '.') }}
                                            </div>
                                            
                                            <form x-ref="addForm" @submit.prevent="addItemAJAX($el)" class="flex items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                                <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                                                <input type="hidden" name="qty" x-model="qty" value="1">
                                                
                                                <button type="submit" :disabled="currentStock <= 0" 
                                                        class="bg-sky-600 hover:bg-sky-700 text-white w-9 h-9 rounded-xl shadow-lg shadow-sky-100 transition-all active:scale-90 flex items-center justify-center disabled:opacity-30 disabled:grayscale shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">Menu tidak tersedia.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: CART -->
            <div class="w-full md:w-[360px] flex flex-col bg-white shrink-0 border-l border-gray-100">
                <div class="p-5 border-b border-gray-100 bg-white">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-black text-gray-800 tracking-tight uppercase italic">Order List</h2>
                        <span class="text-xs font-bold bg-sky-50 text-sky-600 px-3 py-1 rounded-full border border-sky-100">#{{ $activeDraft->id }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm animate-pulse"></div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customer: <span id="current-customer-name" class="text-sky-600 font-black">{{ $activeDraft->pelanggan->nama ?? 'Umum' }}</span></p>
                    </div>
                </div>

                <div class="flex-1 flex flex-col items-center justify-center p-8 bg-gray-50/20 relative">
                    <div class="text-center">
                        <div class="relative inline-block mb-4">
                            <div class="w-16 h-16 bg-sky-50 rounded-2xl flex items-center justify-center text-sky-200"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg></div>
                            <div id="cart-count-badge" class="absolute -top-2 -right-2 w-8 h-8 bg-sky-600 text-white rounded-lg flex items-center justify-center font-black text-sm shadow-lg {{ $activeDraft->details->sum('jumlah') > 0 ? '' : 'hidden' }}">{{ $activeDraft->details->sum('jumlah') }}</div>
                        </div>
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'full-cart-modal')" class="mt-4 w-full py-3 bg-white border-2 border-gray-100 hover:border-sky-300 text-gray-600 hover:text-sky-600 font-bold text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-sm flex items-center justify-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>Detail Belanja</button>
                    </div>
                </div>

                <div class="p-6 bg-white border-t border-gray-100">
                    <div class="mb-6 space-y-1">
                        <div class="flex justify-between items-end"><span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</span><span id="sidebar-cart-subtotal" class="text-sm font-bold text-gray-600 font-mono">Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between items-end pt-1 border-t border-dashed border-gray-100 mt-2"><span class="text-xs font-black text-gray-800 uppercase tracking-tight">Total Akhir</span><span id="sidebar-cart-total" class="text-3xl font-black text-sky-600 tracking-tighter leading-none font-mono">Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('pos.checkout.show', $activeDraft) }}" 
                           id="btn-bayar-sekarang"
                           class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-sky-500/20 transition-all flex items-center justify-center gap-3 active:scale-95 {{ $activeDraft->details->isEmpty() ? 'opacity-50 pointer-events-none grayscale' : '' }}">
                            <span class="text-lg uppercase tracking-wider">BAYAR SEKARANG</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <div class="grid grid-cols-2 gap-3"><a href="{{ route('pos.new_draft') }}" class="flex items-center justify-center bg-yellow-50 text-yellow-700 hover:bg-yellow-100 font-bold py-3.5 rounded-xl text-[10px] uppercase tracking-widest border border-yellow-200 transition-all">Simpan</a><button @click="$dispatch('open-modal', 'confirm-draft-cancel-{{ $activeDraft->id }}')" class="flex items-center justify-center bg-red-50 text-red-700 hover:bg-red-100 font-bold py-3.5 rounded-xl text-[10px] uppercase tracking-widest border border-red-200 transition-all">Batal</button></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS (Isi modal sama seperti sebelumnya, hanya memastikan fungsi-fungsinya jalan) -->
        <x-modal :name="'confirm-draft-cancel-'.$activeDraft->id" focusable>
            <form method="post" action="{{ route('pos.cancel_draft') }}" class="p-8 text-center">
                @csrf
                <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}"><div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-red-50"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></div><h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight text-center">Hapus Pesanan?</h2><div class="mt-10 flex justify-center gap-4"><button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-gray-100 text-gray-700 font-black rounded-2xl uppercase tracking-widest text-xs transition-colors">Tutup</button><button type="submit" class="flex-1 py-4 bg-red-600 text-white font-black rounded-2xl uppercase tracking-widest text-xs transition-all shadow-xl shadow-red-100">Ya, Hapus</button></div>
            </form>
        </x-modal>

        <x-modal name="full-cart-modal" focusable maxWidth="4xl">
            <div class="p-8 bg-white rounded-3xl h-[85vh] flex flex-col" x-data="{ step: 'view', selectedItems: [], items: {{ $activeDraft->details->map(function($item) { return ['id' => $item->id, 'produk_id' => $item->produk_id, 'nama_produk' => $item->produk->nama_produk, 'kategori' => $item->produk->kategori->nama ?? '-', 'stok_asli' => $item->produk->stok + $item->jumlah, 'harga_satuan' => number_format($item->harga_satuan, 0, ',', '.'), 'jumlah' => $item->jumlah, 'subtotal' => number_format($item->subtotal, 0, ',', '.') ]; })->toJson() }}, total: '{{ number_format($activeDraft->total, 0, ',', '.') }}', updateData(newData) { this.items = newData.details; this.total = newData.total_format; }, toggleSelectItem(id, maxQty) { const idx = this.selectedItems.findIndex(i => i.detail_id === id); if (idx > -1) this.selectedItems.splice(idx, 1); else this.selectedItems.push({ detail_id: id, qty: maxQty }); }, async executeSplit() { if (this.selectedItems.length === 0) return; try { let res = await fetch('{{ route('pos.split_bill') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ transaksi_id: {{ $activeDraft->id }}, items: this.selectedItems }) }); let data = await res.json(); if (res.ok) { Toastify({ text: data.message, duration: 3000, style: { background: '#10b981' } }).showToast(); window.location.reload(); } } catch (e) { } } }" x-on:cart-updated.window="updateData($event.detail)">
                <div class="flex justify-between items-center mb-8 border-b pb-6 shrink-0"><div><h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight italic" x-text="step === 'view' ? 'Rincian Belanja' : 'Pisah Nota (Pilih Menu)'"></h2><p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-widest">ORDER #{{ $activeDraft->id }}</p></div><div class="text-right"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pembayaran</p><p class="text-4xl font-black text-sky-600 tracking-tighter" x-text="'Rp ' + total"></p></div></div>
                <div class="flex-1 overflow-y-auto custom-scrollbar -mx-2 px-2"><table class="w-full text-left"><thead class="bg-gray-50/80 sticky top-0 z-10 border-b border-gray-100"><tr><th x-show="step === 'split'" class="py-4 px-4 w-10"></th><th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Menu</th><th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Harga</th><th class="py-3 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qty</th><th class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Subtotal</th><th x-show="step === 'view'" class="py-4 px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th></tr></thead><tbody class="divide-y divide-gray-100"><template x-for="item in items" :key="item.id"><tr class="hover:bg-sky-50/30 transition-colors" :class="selectedItems.find(i => i.detail_id === item.id) ? 'bg-sky-50' : ''"><td x-show="step === 'split'" class="py-5 px-4 text-center"><input type="checkbox" @change="toggleSelectItem(item.id, item.jumlah)" class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500"></td><td class="py-5 px-4"><div class="flex items-center gap-4"><div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 font-black text-xs shrink-0" x-text="item.nama_produk.substring(0, 2).toUpperCase()"></div><div><div class="font-black text-gray-800 text-base leading-tight" x-text="item.nama_produk"></div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1" x-text="item.kategori"></div></div></div></td><td class="py-5 px-4 text-right font-bold text-gray-600" x-text="'Rp ' + item.harga_satuan"></td><td class="py-5 px-4 text-center"><div x-show="step === 'view'" class="inline-flex items-center bg-gray-100 rounded-2xl p-1 border border-gray-200/50 shadow-inner"><button type="button" @click="if(item.jumlah > 1) updateItemQty(item.id, item.jumlah - 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-sky-600 transition-colors bg-white rounded-xl shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg></button><div class="w-10 text-sm font-black text-gray-800" x-text="item.jumlah"></div><button type="button" @click="if(item.jumlah < item.stok_asli) updateItemQty(item.id, item.jumlah + 1)" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-sky-600 transition-colors bg-white rounded-xl shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg></button></div><div x-show="step === 'split'" class="font-black text-gray-800" x-text="item.jumlah"></div></td><td class="py-5 px-4 text-right font-black text-gray-800" x-text="'Rp ' + item.subtotal"></td><td x-show="step === 'view'" class="py-5 px-4 text-center"><button @click="removeItemAJAX(item.id)" class="p-2 text-gray-300 hover:text-rose-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td></tr></template></tbody></table></div>
                <div class="mt-8 pt-8 border-t border-gray-100 flex justify-between items-center shrink-0"><div><button x-show="step === 'view' && items.length > 1" @click="step = 'split'" class="px-6 py-3 bg-amber-50 text-amber-700 font-black rounded-2xl uppercase tracking-widest text-[10px] hover:bg-amber-100 transition-colors flex items-center gap-2 border border-amber-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L5 19m0-14l4.121 4.121"></path></svg>Split Bill</button><button x-show="step === 'split'" @click="step = 'view'; selectedItems = []" class="text-gray-400 font-bold text-xs uppercase tracking-widest">Batal</button></div><div class="flex gap-4"><button type="button" x-on:click="$dispatch('close')" class="px-10 py-4 bg-gray-100 text-gray-700 font-black rounded-2xl uppercase tracking-widest text-xs hover:bg-gray-200 transition-colors">Tutup</button><a x-show="step === 'view'" href="{{ route('pos.checkout.show', $activeDraft) }}" class="px-10 py-4 bg-sky-600 text-white font-black rounded-2xl shadow-xl shadow-sky-100 hover:bg-sky-700 uppercase tracking-widest text-xs transition-all active:scale-95" :class="items.length === 0 ? 'hidden' : ''">Lanjutkan &rarr;</a><button x-show="step === 'split'" @click="executeSplit()" :disabled="selectedItems.length === 0" class="px-10 py-4 bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-100 hover:bg-emerald-700 uppercase tracking-widest text-xs transition-all active:scale-95 disabled:opacity-50">Pisahkan Nota</button></div></div>
            </div>
        </x-modal>

        <x-modal name="scan-barcode-modal" focusable maxWidth="md">
            <div class="p-8" x-data="{ scanMode: 'manual' }" x-init="$watch('scanMode', val => { if(val === 'manual') setTimeout(() => $refs.manualInput.focus(), 100); else if(val === 'camera') startCameraScan(); else stopBarcodeScanner(); })">
                <div class="flex justify-between items-center mb-8"><h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight italic">Scan Barcode</h2><button type="button" onclick="stopBarcodeScanner(); window.dispatchEvent(new CustomEvent('close-modal', { detail: 'scan-barcode-modal' }));" class="text-gray-400 hover:text-gray-600"><svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="flex p-1.5 bg-gray-100 rounded-2xl mb-8"><button @click="scanMode = 'manual'; stopBarcodeScanner();" :class="scanMode === 'manual' ? 'bg-white text-sky-600 shadow-sm' : 'text-gray-500'" class="flex-1 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Manual</button><button @click="scanMode = 'camera'" :class="scanMode === 'camera' ? 'bg-white text-sky-600 shadow-sm' : 'text-gray-500'" class="flex-1 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Kamera</button></div><div x-show="scanMode === 'manual'" class="bg-sky-50 p-10 rounded-[40px] border border-sky-100 text-center shadow-inner"><input type="text" id="manual-barcode-input" x-ref="manualInput" class="w-full text-center border-none bg-white rounded-3xl py-6 px-6 focus:ring-4 focus:ring-sky-500/20 font-black text-4xl tracking-widest shadow-xl" placeholder="SCAN..." onkeydown="if(event.key === 'Enter') { event.preventDefault(); processQuickScan(this.value); this.value = ''; }"><p class="mt-6 text-sky-600 font-bold text-[10px] uppercase tracking-widest animate-pulse">Menunggu Barcode...</p></div><div x-show="scanMode === 'camera'" style="display: none;"><div class="bg-black rounded-[40px] overflow-hidden relative h-[350px] shadow-2xl border-8 border-gray-900"><div id="reader-barcode" class="w-full h-full object-cover"></div><div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-gray-900/80"><svg class="w-12 h-12 mb-4 animate-spin text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg><p class="text-xs font-black uppercase tracking-widest">Memulai Kamera...</p></div></div><p class="mt-6 text-center text-gray-400 font-bold text-[10px] uppercase tracking-widest">Arahkan kamera ke Barcode produk</p></div>
            </div>
        </x-modal>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;
        let isProcessingScan = false;
        let lastScanTime = 0;
        const beepSound = new Audio("https://www.soundjay.com/buttons/sounds/button-3.mp3");

        function initScanner() { if (html5QrCode === null) html5QrCode = new Html5Qrcode("reader-barcode"); }
        function startCameraScan() { initScanner(); document.getElementById('camera-placeholder').style.display = 'none'; html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onBarcodeScanSuccess).catch(err => { document.getElementById('camera-placeholder').style.display = 'flex'; }); }
        function stopBarcodeScanner() { if (html5QrCode && html5QrCode.isScanning) html5QrCode.stop().then(() => html5QrCode.clear()); }
        function onBarcodeScanSuccess(decodedText) { processQuickScan(decodedText); if(html5QrCode && html5QrCode.isScanning) { html5QrCode.pause(); setTimeout(() => html5QrCode.resume(), 2000); } }

        function refreshAllProductStocks(cartDetails) {
            document.querySelectorAll('.product-card').forEach(card => {
                const id = card.getAttribute('data-produk-id');
                const asli = parseInt(card.getAttribute('data-stok-asli'));
                window.dispatchEvent(new CustomEvent('stock-updated-' + id, { detail: { available: asli } }));
            });
            cartDetails.forEach(item => {
                const card = document.querySelector(`.product-card[data-produk-id="${item.produk_id}"]`);
                if (card) {
                    const asli = parseInt(card.getAttribute('data-stok-asli'));
                    window.dispatchEvent(new CustomEvent('stock-updated-' + item.produk_id, { detail: { available: asli - item.jumlah } }));
                }
            });
        }

        async function addItemAJAX(form) {
            if(isProcessingScan) return;
            isProcessingScan = true;
            const formData = new FormData(form);
            try {
                let res = await fetch('{{ route("pos.add_item") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData });
                let result = await res.json();
                if (result.status === 'success') { beepSound.play().catch(() => {}); Toastify({ text: result.message, duration: 2000, style: { background: '#10b981' } }).showToast(); updateUIFromCart(result.data); }
                else { Toastify({ text: result.message, duration: 3000, style: { background: '#ef4444' } }).showToast(); }
            } catch (err) { } finally { isProcessingScan = false; }
        }

        async function removeItemAJAX(detailId) {
            try {
                let res = await fetch('{{ route("pos.remove_item") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ transaksi_detail_id: detailId }) });
                let result = await res.json();
                if (result.status === 'success') { Toastify({ text: result.message, duration: 2000, style: { background: '#10b981' } }).showToast(); updateUIFromCart(result.data); }
            } catch (err) { }
        }

        function updateUIFromCart(data) {
            const sbTotal = document.getElementById('sidebar-cart-total');
            const sbSub = document.getElementById('sidebar-cart-subtotal');
            const btnPay = document.getElementById('btn-bayar-sekarang');
            if(sbTotal) sbTotal.innerText = 'Rp ' + data.total_format;
            if(sbSub) sbSub.innerText = 'Rp ' + data.total_format;
            const badge = document.getElementById('cart-count-badge');
            if(badge) { badge.innerText = data.cart_count; badge.classList.toggle('hidden', data.cart_count === 0); }
            if(btnPay) { if(data.cart_count > 0) btnPay.classList.remove('opacity-50', 'pointer-events-none', 'grayscale'); else btnPay.classList.add('opacity-50', 'pointer-events-none', 'grayscale'); }
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            refreshAllProductStocks(data.details);
        }

        async function processQuickScan(barcode) {
            if(!barcode || isProcessingScan) return;
            const now = Date.now();
            if (now - lastScanTime < 800) return;
            isProcessingScan = true;
            lastScanTime = now;
            try {
                let res = await fetch('{{ route("pos.scan") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ barcode: barcode, transaksi_id: '{{ $activeDraft->id }}' }) });
                let result = await res.json();
                if (result.status === 'success') { beepSound.play().catch(() => {}); Toastify({ text: result.message, duration: 2000, style: { background: '#10b981' } }).showToast(); updateUIFromCart(result.data); if(result.data.pelanggan) document.getElementById('current-customer-name').innerText = result.data.pelanggan; }
                else { Toastify({ text: result.message, duration: 3000, style: { background: '#ef4444' } }).showToast(); }
            } catch (err) { } finally { isProcessingScan = false; }
        }

        function updateItemQty(detailId, newQty) {
            if(newQty < 1 || isProcessingScan) return;
            isProcessingScan = true;
            fetch('{{ route("pos.update_item") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ transaksi_detail_id: detailId, qty: newQty }) })
            .then(res => res.json()).then(result => { if(result.status === 'success') updateUIFromCart(result.data); else Toastify({ text: result.message, duration: 3000, style: { background: '#ef4444' } }).showToast(); })
            .finally(() => { isProcessingScan = false; });
        }

        let bBuf = ''; let bTime = null;
        document.addEventListener('keydown', e => {
            if (e.key.startsWith('F') && e.key !== 'F5') { e.preventDefault(); return; }
            if (e.ctrlKey && e.shiftKey && ['I', 'J', 'C', 'K', 'L'].includes(e.key.toUpperCase())) { e.preventDefault(); return; }
            if (document.activeElement.id === 'pos-search-input') return;
            if (e.key.length === 1) { bBuf += e.key; if (bTime) clearTimeout(bTime); bTime = setTimeout(() => { bBuf = ''; }, 150); }
            else if (e.key === 'Enter') { if(bBuf.length >= 3) { e.preventDefault(); e.stopImmediatePropagation(); processQuickScan(bBuf); bBuf = ''; } }
        }, true);

        const sInp = document.getElementById('pos-search-input');
        if (sInp) sInp.addEventListener('keydown', e => { if (e.key === 'Enter') { const v = e.target.value.trim(); if (v.length >= 3) { e.preventDefault(); processQuickScan(v); e.target.value = ''; } } });

        window.addEventListener('load', () => {
            const p = new URLSearchParams(window.location.search);
            const om = p.get('open_modal');
            if (om) window.dispatchEvent(new CustomEvent('open-modal', { detail: om }));
            @if($activeDraft->details->isNotEmpty())
                refreshAllProductStocks({!! $activeDraft->details->map(fn($it)=>(['produk_id'=>$it->produk_id,'jumlah'=>$it->jumlah]))->toJson() !!});
            @endif
        });
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</x-app-layout>
