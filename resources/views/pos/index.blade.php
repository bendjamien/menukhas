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
            <div class="bg-white p-5 rounded-lg shadow sticky top-24 h-[calc(100vh-115px)] flex flex-col border border-gray-100 overflow-y-auto custom-scrollbar">
                
                <!-- HEADER: Info Transaksi -->
                <div class="flex justify-between items-center border-b pb-3 mb-3 shrink-0">
                    <h2 class="text-lg font-bold text-gray-800 tracking-tight">Keranjang</h2>
                    <span class="text-xs font-mono font-bold text-sky-600 bg-sky-50 px-2 py-1 rounded">#{{ $activeDraft->id }}</span>
                </div>

                <!-- INFO: Pending Drafts -->
                @if($pendingDrafts->isNotEmpty())
                    <div x-data="{ open: false }" class="relative mb-3 shrink-0">
                        <button @click="open = !open" class="w-full flex justify-between items-center px-3 py-2 bg-yellow-50 text-yellow-700 text-[10px] font-bold rounded-lg hover:bg-yellow-100 border border-yellow-200">
                            <span>⚠️ {{ $pendingDrafts->count() }} Draft Ditahan</span>
                            <svg :class="{'rotate-180': open}" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-30 mt-1 w-full bg-white rounded-lg shadow-xl border border-gray-100 max-h-40 overflow-y-auto">
                            @foreach ($pendingDrafts as $draft)
                                <a href="{{ route('pos.index', ['transaksi' => $draft->id]) }}" class="block px-3 py-2 hover:bg-gray-50 border-b last:border-b-0 transition">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-[10px] text-gray-800">{{ $draft->pelanggan->nama ?? 'Umum' }}</span>
                                        <span class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($draft->tanggal)->format('H:i') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- BODY: Keranjang Summary (Shrinkable) -->
                <div class="flex-1 min-h-0 flex flex-col justify-center items-center p-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 mb-4">
                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center relative mb-2 shadow-sm border border-slate-100 shrink-0">
                        <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        @if($activeDraft->details->sum('jumlah') > 0)
                            <div class="absolute -top-1 -right-1 w-7 h-7 bg-sky-600 rounded-full flex items-center justify-center text-white font-black text-[10px] shadow-lg border-2 border-white animate-bounce">
                                {{ $activeDraft->details->sum('jumlah') }}
                            </div>
                        @endif
                    </div>
                    <div class="text-center shrink-0">
                        <h3 class="text-sm font-bold text-slate-700 leading-tight">Pesanan Tersimpan</h3>
                        <p class="text-[10px] text-slate-400">Gunakan tombol cek detail.</p>
                    </div>
                </div>

                <!-- FOOTER: Totals & Actions (Fixed at Bottom) -->
                <div class="border-t pt-3 space-y-3 shrink-0">
                    <div class="space-y-0.5">
                        <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-2xl font-black text-sky-600 tracking-tighter">
                            <span>Total</span>
                            <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'full-cart-modal')"
                                class="w-full flex items-center justify-center bg-white text-slate-600 hover:bg-slate-50 font-bold py-3 px-4 rounded-xl border-2 border-slate-100 hover:border-sky-200 transition-all text-sm gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Cek Detail Pesanan
                        </button>

                        <a href="{{ route('pos.checkout.show', $activeDraft) }}"
                           class="w-full flex items-center justify-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 px-4 rounded-xl shadow-lg shadow-sky-500/20 transition-all transform hover:-translate-y-0.5 {{ $activeDraft->details->isEmpty() ? 'opacity-50 pointer-events-none grayscale' : '' }}">
                            <span class="mr-2 text-lg">BAYAR SEKARANG</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        
                        <div class="grid grid-cols-2 gap-3 mt-1 pb-2">
                            <a href="{{ route('pos.new_draft') }}"
                               class="flex items-center justify-center bg-yellow-50 text-yellow-700 hover:bg-yellow-100 font-bold py-3.5 px-4 rounded-xl border border-yellow-200 transition-all text-sm">
                                Simpan
                            </a>
                            <button type="button"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-draft-cancel-{{ $activeDraft->id }}')"
                                    class="flex items-center justify-center bg-red-50 text-red-700 hover:bg-red-100 font-bold py-3.5 px-4 rounded-xl border border-red-200 transition-all text-sm">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

            </div>
        </div>

            </div>
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

        <!-- Modal Cek Detail Pesanan (Full View) -->
    <x-modal name="full-cart-modal" focusable maxWidth="4xl">
        <div class="p-6 bg-white rounded-2xl h-[80vh] flex flex-col">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan</h2>
                    <p class="text-sm text-gray-500">No. Transaksi: <span class="font-mono font-bold text-sky-600">#{{ $activeDraft->id }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Sementara</p>
                    <p class="text-2xl font-extrabold text-sky-600">Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar -mx-2 px-2">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="py-3 px-4 font-semibold text-gray-600 text-sm rounded-l-lg">Produk</th>
                            <th class="py-3 px-4 font-semibold text-gray-600 text-sm text-right">Harga</th>
                            <th class="py-3 px-4 font-semibold text-gray-600 text-sm text-center">Qty</th>
                            <th class="py-3 px-4 font-semibold text-gray-600 text-sm text-right">Subtotal</th>
                            <th class="py-3 px-4 font-semibold text-gray-600 text-sm text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($activeDraft->details as $item)
                        <tr class="hover:bg-sky-50/50 transition-colors group">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-xs shrink-0">
                                        {{ substr($item->produk->nama_produk, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $item->produk->nama_produk }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->produk->kategori->nama ?? '-' }} | Kode: {{ $item->produk->kode_barcode ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right font-mono text-gray-600">
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-12 h-10 bg-gray-100 rounded-lg font-bold text-gray-800 border border-gray-200">
                                    {{ $item->jumlah }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right font-bold text-gray-800 font-mono">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <form action="{{ route('pos.remove_item') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                    <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p class="text-lg font-medium">Keranjang masih kosong</p>
                                <p class="text-sm">Scan barcode atau pilih produk untuk menambahkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" 
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    Tutup & Lanjut Belanja
                </button>
                <a href="{{ route('pos.checkout.show', $activeDraft) }}" 
                   class="px-6 py-2.5 bg-sky-600 text-white font-bold rounded-xl shadow-lg shadow-sky-200 hover:bg-sky-700 transition-transform transform hover:-translate-y-0.5 {{ $activeDraft->details->isEmpty() ? 'hidden' : '' }}">
                    Lanjut ke Pembayaran &rarr;
                </a>
            </div>
        </div>
    </x-modal>

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
    <x-modal name="scan-barcode-modal" focusable maxWidth="md">
        <div class="p-5" x-data="{ scanMode: 'manual' }" x-init="$watch('scanMode', val => { if(val === 'manual') setTimeout(() => $refs.manualInput.focus(), 100); else if(val === 'camera') startCameraScan(); else stopBarcodeScanner(); })">
            
            <!-- Header -->
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Scan Produk
                </h2>
                <button type="button" onclick="stopBarcodeScanner(); window.dispatchEvent(new CustomEvent('close-modal', { detail: 'scan-barcode-modal' }));" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Tab Navigasi -->
            <div class="flex p-1 bg-gray-100 rounded-xl mb-4">
                <button @click="scanMode = 'manual'; stopBarcodeScanner();" 
                        :class="scanMode === 'manual' ? 'bg-white text-sky-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                    Input / Scanner
                </button>
                <button @click="scanMode = 'camera'" 
                        :class="scanMode === 'camera' ? 'bg-white text-sky-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-2 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22a2 2 0 001.664.89H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                    Kamera HP
                </button>
            </div>

            <!-- MODE 1: MANUAL INPUT -->
            <div x-show="scanMode === 'manual'" class="space-y-4">
                <div class="bg-sky-50 p-4 rounded-xl border border-sky-100 text-center">
                    <label class="block text-xs font-bold text-sky-700 uppercase mb-2">Kode Barcode</label>
                    <input type="text" 
                           id="manual-barcode-input"
                           x-ref="manualInput"
                           class="w-full text-center border-2 border-sky-300 rounded-lg py-3 px-4 focus:outline-none focus:ring-4 focus:ring-sky-200 font-mono text-xl font-bold placeholder-sky-300 transition-all bg-white"
                           placeholder="Scan / Ketik..."
                           onkeypress="if(event.key === 'Enter') { processQuickScan(this.value); this.value = ''; }">
                    <p class="text-[10px] text-sky-600 mt-2 font-medium">Tekan ENTER setelah input.</p>
                </div>
                
                <div class="text-center">
                    <p class="text-xs text-gray-400">Gunakan scanner fisik atau ketik kode manual.</p>
                </div>
            </div>

            <!-- MODE 2: KAMERA -->
            <div x-show="scanMode === 'camera'" style="display: none;" class="space-y-4">
                <div class="bg-black rounded-xl overflow-hidden relative h-[250px] border-2 border-gray-800 shadow-inner">
                    <div id="reader-barcode" class="w-full h-full object-cover"></div>
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-10 h-10 mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22a2 2 0 001.664.89H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        <p class="text-xs">Menyiapkan kamera...</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <button onclick="document.getElementById('barcode-file-input').click()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-2.5 rounded-lg text-sm font-bold flex items-center justify-center gap-2 transition w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Foto Barcode
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
                document.getElementById('camera-placeholder').style.display = 'flex';
                document.getElementById('camera-placeholder').innerHTML = '<p class="text-red-500 text-xs text-center px-4">Kamera tidak dapat diakses.<br>Pastikan izin browser diberikan.</p>';
            });
        }

        // 2. SCAN PAKE FILE (UPLOAD)
        document.getElementById('barcode-file-input').addEventListener('change', e => {
            if (e.target.files.length == 0) return;
            
            const imageFile = e.target.files[0];
            initScanner();

            // Scan file
            html5QrCode.scanFile(imageFile, true)
            .then(decodedText => onBarcodeScanSuccess(decodedText))
            .catch(err => {
                Toastify({ text: "Barcode tidak terbaca.", duration: 3000, style: { background: "#ef4444" } }).showToast();
            });
        });

        function stopBarcodeScanner() {
            if (html5QrCode) {
                if(html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        document.getElementById('camera-placeholder').style.display = 'flex';
                    }).catch(err => console.log(err));
                }
            }
        }

        // LOGIKA SUKSES SCAN (SAMA)
        function onBarcodeScanSuccess(decodedText, decodedResult) {
            processQuickScan(decodedText);
            
            if(html5QrCode && html5QrCode.isScanning) {
                html5QrCode.pause(); 
                setTimeout(() => html5QrCode.resume(), 2000);
            }
        }

        function processQuickScan(barcode) {
            if(!barcode) return;

            fetch('{{ route("pos.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    barcode: barcode,
                    transaksi_id: '{{ $activeDraft->id }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    beepSound.play().catch(() => {});
                    Toastify({ text: data.message, duration: 2000, style: { background: "#10b981" } }).showToast();
                    setTimeout(() => { window.location.reload(); }, 500); 
                } else {
                    Toastify({ text: data.message, duration: 3000, style: { background: "#ef4444" } }).showToast();
                }
            })
            .catch(error => console.error('Error:', error));
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