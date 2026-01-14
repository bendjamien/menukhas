<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                <form action="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" method="GET">
                    <div class="relative flex gap-2">
                        <div class="relative flex-grow">
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
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'scan-barcode-modal'); startBarcodeScanner()"
                                class="bg-gray-800 hover:bg-gray-900 text-white px-4 rounded-lg shadow-sm transition flex items-center gap-2" title="Scan Barcode">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span class="hidden md:inline font-bold">Scan</span>
                        </button>
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

                <div class="py-2 space-y-2">
                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-member-modal')"
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Daftar Member Baru
                    </button>

                    <!-- Kode Promo / Voucher (Auto Check) -->
                    <div class="relative">
                        <input type="text" id="voucher-code" placeholder="Masukan Voucher..." 
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-sky-500 focus:ring-sky-500 uppercase font-bold text-center tracking-widest"
                               oninput="debounceCheckVoucher()">
                        <div id="voucher-loading" class="absolute right-3 top-2.5 hidden">
                            <svg class="animate-spin h-5 w-5 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div id="voucher-message" class="hidden text-xs font-bold text-center mt-1"></div>
                </div>

                <div class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    @forelse ($activeDraft->details as $item)
                        <div class="flex justify-between items-start group bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <div class="flex-grow pr-2">
                                <span class="block text-sm font-medium text-gray-800 line-clamp-2">{{ $item->produk->nama_produk ?? 'Item dihapus' }}</span>
                                <span class="block text-xs text-gray-500 mt-0.5">@ Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span id="item-subtotal-{{ $item->id }}" class="text-sm font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center bg-white rounded-lg border border-gray-300 shadow-sm overflow-hidden h-9">
                                        <button type="button" 
                                                onclick="let input = document.getElementById('qty-{{ $item->id }}'); if(input.value > 1) { input.value--; updateItemQty('{{ $item->id }}', input.value); }"
                                                class="h-full px-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors border-r border-gray-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                        
                                        <input type="number" id="qty-{{ $item->id }}" value="{{ $item->jumlah }}" min="1"
                                                class="w-12 h-full text-center border-none p-0 text-base font-bold focus:ring-0 appearance-none"
                                                onchange="updateItemQty('{{ $item->id }}', this.value)">

                                        <button type="button" 
                                                onclick="let input = document.getElementById('qty-{{ $item->id }}'); input.value++; updateItemQty('{{ $item->id }}', input.value);"
                                                class="h-full px-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors border-l border-gray-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>

                                    <form action="{{ route('pos.remove_item') }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors" title="Hapus Item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                            <span id="cart-subtotal">Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-sky-600">
                            <span>Total</span>
                            <span id="cart-total">Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
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

        <!-- Modal Tambah Member Baru -->
        <x-modal name="add-member-modal" focusable>
            <form method="post" action="{{ route('pos.store_member') }}" class="p-6">
                @csrf
                <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                
                <div class="flex justify-between items-center mb-6">                <h2 class="text-xl font-bold text-gray-900">
                    Daftar Member Baru
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor HP / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" name="no_hp" required placeholder="08xxxxxxxxxx" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Digunakan untuk identifikasi member saat checkout.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat (Opsional)</label>
                        <input type="text" name="alamat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                    Simpan Member
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Scan Barcode -->
    <x-modal name="scan-barcode-modal" focusable maxWidth="sm">
        <div class="p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Scan Barcode
                </h2>
                <button type="button" onclick="stopBarcodeScanner(); window.dispatchEvent(new CustomEvent('close-modal', { detail: 'scan-barcode-modal' }));" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Area Kamera -->
                <div class="bg-gray-100 rounded-lg overflow-hidden relative min-h-[250px] border border-gray-300">
                    <div id="reader-barcode" class="w-full h-full"></div>
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22a2 2 0 001.664.89H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <p class="text-sm">Kamera belum aktif</p>
                    </div>
                </div>

                <!-- Tombol Kontrol -->
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="startCameraScan()" class="bg-sky-600 hover:bg-sky-700 text-white py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Aktifkan Kamera
                    </button>
                    <button onclick="document.getElementById('barcode-file-input').click()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Foto
                    </button>
                    <input type="file" id="barcode-file-input" accept="image/*" class="hidden">
                </div>
            </div>
        </div>
    </x-modal>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let html5QrCode = null;
        const beepSound = new Audio("https://www.soundjay.com/buttons/sounds/button-3.mp3");

        function initScanner() {
            if (html5QrCode === null) {
                html5QrCode = new Html5Qrcode("reader-barcode");
            }
        }

        // 1. SCAN PAKE KAMERA
        function startCameraScan() {
            initScanner();
            document.getElementById('camera-placeholder').style.display = 'none';
            
            const config = { fps: 10, qrbox: { width: 250, height: 150 }, aspectRatio: 1.0 };
            
            html5QrCode.start({ facingMode: "environment" }, config, onBarcodeScanSuccess)
            .catch(err => {
                console.error("Gagal start kamera", err);
                Toastify({ text: "Gagal akses kamera.", duration: 3000, style: { background: "#ef4444" } }).showToast();
            });
        }

        // 2. SCAN PAKE FILE (UPLOAD)
        document.getElementById('barcode-file-input').addEventListener('change', e => {
            if (e.target.files.length == 0) return;
            
            const imageFile = e.target.files[0];
            initScanner();

            // Scan file
            html5QrCode.scanFile(imageFile, true)
            .then(decodedText => {
                onBarcodeScanSuccess(decodedText);
            })
            .catch(err => {
                console.error("Gagal scan file", err);
                Toastify({ text: "Barcode tidak terbaca di gambar ini.", duration: 3000, style: { background: "#ef4444" } }).showToast();
            });
        });

        function stopBarcodeScanner() {
            if (html5QrCode) {
                if(html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        document.getElementById('camera-placeholder').style.display = 'flex';
                    }).catch(err => console.log(err));
                }
            }
        }

        // LOGIKA SUKSES SCAN (SAMA)
        function onBarcodeScanSuccess(decodedText, decodedResult) {
            beepSound.play().catch(e => console.log(e));
            
            if(html5QrCode.isScanning) {
                html5QrCode.pause(); 
            }

            fetch('{{ route("pos.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    barcode: decodedText,
                    transaksi_id: '{{ $activeDraft->id }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Toastify({ text: data.message, duration: 2000, style: { background: "#10b981" } }).showToast();
                    setTimeout(() => { window.location.reload(); }, 500); 
                } else {
                    Toastify({ text: data.message, duration: 3000, style: { background: "#ef4444" } }).showToast();
                    if(html5QrCode.isScanning) html5QrCode.resume();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Toastify({ text: "Gagal memproses barcode.", duration: 3000, style: { background: "#ef4444" } }).showToast();
                if(html5QrCode.isScanning) html5QrCode.resume();
            });
        }

        window.addEventListener('close-modal', event => {
            if (event.detail === 'scan-barcode-modal') {
                stopBarcodeScanner();
            }
        });
    </script>

    <script>
        function updateItemQty(detailId, newQty) {
            // Prevent negative or zero
            if(newQty < 1) return;

            fetch('{{ route('pos.update_item') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    transaksi_detail_id: detailId,
                    qty: newQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update Item Subtotal
                    const itemSubtotalEl = document.getElementById('item-subtotal-' + detailId);
                    if(itemSubtotalEl) itemSubtotalEl.innerText = 'Rp ' + data.item_subtotal;

                    // Update Cart Total & Subtotal
                    const cartSubtotalEl = document.getElementById('cart-subtotal');
                    const cartTotalEl = document.getElementById('cart-total');
                    
                    if(cartSubtotalEl) cartSubtotalEl.innerText = 'Rp ' + data.transaksi_total;
                    if(cartTotalEl) cartTotalEl.innerText = 'Rp ' + data.transaksi_total;
                } else {
                    // Show Error Toast if available
                    if(typeof Toastify === 'function') {
                        Toastify({ text: data.message || 'Gagal update qty', duration: 3000, style: { background: "#ef4444" } }).showToast();
                    } else {
                        alert(data.message || 'Gagal update qty');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        let voucherTimeout = null;

        function debounceCheckVoucher() {
            clearTimeout(voucherTimeout);
            const messageEl = document.getElementById('voucher-message');
            const loadingEl = document.getElementById('voucher-loading');
            
            messageEl.classList.add('hidden');
            loadingEl.classList.remove('hidden');

            voucherTimeout = setTimeout(() => {
                checkVoucher();
            }, 800); // Wait 800ms after typing stops
        }

        function checkVoucher() {
            const code = document.getElementById('voucher-code').value;
            const messageEl = document.getElementById('voucher-message');
            const loadingEl = document.getElementById('voucher-loading');
            
            if(!code) {
                loadingEl.classList.add('hidden');
                return;
            }

            fetch('{{ route('pos.check_voucher') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kode: code,
                    transaksi_id: '{{ $activeDraft->id }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                loadingEl.classList.add('hidden');
                messageEl.classList.remove('hidden', 'text-green-600', 'text-red-500');
                
                if(data.status === 'success') {
                    messageEl.classList.add('text-green-600');
                    messageEl.innerText = "Voucher Aktif: " + data.voucher_info;
                    Toastify({ text: "Voucher berhasil diterapkan!", duration: 3000, style: { background: "#10b981" } }).showToast();
                    
                    // Reload to update totals
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    messageEl.classList.add('text-red-500');
                    messageEl.innerText = data.message;
                }
            })
            .catch(err => {
                console.error(err);
                loadingEl.classList.add('hidden');
            });
        }
    </script>
</x-app-layout>