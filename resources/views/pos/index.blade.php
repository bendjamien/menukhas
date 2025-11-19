<x-app-layout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <form action="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                               placeholder="Cari produk berdasarkan nama atau barcode..." 
                               class="w-full border-gray-300 rounded-lg shadow-sm pl-10 focus:border-sky-500 focus:ring-sky-500">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        @if($search)
                            <a href="{{ route('pos.index', ['transaksi' => $activeDraft->id]) }}" class="absolute right-3 top-2.5 text-gray-500 hover:text-red-500" title="Reset Pencarian">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 p-4 h-[60vh] overflow-y-auto">
                    @forelse ($produks as $produk)
                        <form action="{{ route('pos.add_item') }}" method="POST">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                            <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                            <button type="submit" 
                                    class="w-full text-left p-3 border rounded-lg hover:shadow-lg hover:border-sky-500 transition duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500 flex flex-col">
                                <div class="w-full h-24 bg-gray-100 rounded-md flex items-center justify-center mb-2">
                                    <span class="text-gray-400 text-xs">Gambar</span>
                                </div>
                                <div>
                                    <span class="block text-sm font-semibold text-gray-800 line-clamp-2 h-10">{{ $produk->nama_produk }}</span>
                                    <span class="block text-xs text-gray-500">{{ $produk->kategori->nama ?? 'N/A' }}</span>
                                </div>
                                <span class="block text-sm font-bold text-sky-600 mt-1">
                                    Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                </span>
                            </button>
                        </form>
                    @empty
                        <p class="text-gray-500 col-span-full text-center">Produk tidak ditemukan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow space-y-6 sticky top-24">
                
                <h2 class="text-xl font-bold text-gray-800 border-b pb-3">
                    Keranjang
                    <span class="text-sm font-normal text-gray-400">(#{{ $activeDraft->id }})</span>
                </h2>

                @if($pendingDrafts->isNotEmpty())
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            @if(Auth::user()->role == 'admin')
                                Semua Transaksi Ditahan:
                            @else
                                Transaksi Anda yang Ditahan:
                            @endif
                        </label>
                        <div class="max-h-24 overflow-y-auto space-y-2">
                            @foreach ($pendingDrafts as $draft)
                                <a href="{{ route('pos.index', ['transaksi' => $draft->id]) }}" 
                                   class="block w-full text-left p-2.5 border rounded-lg bg-gray-50 hover:bg-gray-100">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-sm text-gray-800">
                                            {{ $draft->pelanggan->nama ?? 'Pelanggan Umum' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ({{ $draft->details->count() }} item)
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex justify-between">
                                        <span>Ditahan @ {{ \Carbon\Carbon::parse($draft->tanggal)->format('H:i') }}</span>
                                        @if(Auth::user()->role == 'admin')
                                            <span class="font-medium">{{ $draft->kasir->name ?? 'N/A' }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('pos.save_customer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                    <label for="pelanggan_id" class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                    <select id="pelanggan_id" name="pelanggan_id" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500"
                            onchange="this.form.submit()">
                        <option value="">-- Pelanggan Umum --</option>
                        @foreach ($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->id }}"
                                @if($activeDraft->pelanggan_id == $pelanggan->id) selected @endif
                            >
                                {{ $pelanggan->nama }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="space-y-3 h-64 overflow-y-auto pr-2">
                    @forelse ($activeDraft->details as $item)
                        <div class="flex justify-between items-center">
                            <div class="flex-grow pr-2">
                                <span class="block text-sm font-medium text-gray-800 line-clamp-1">{{ $item->produk->nama_produk ?? 'N/A' }}</span>
                                <span class="block text-xs text-gray-500">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <form action="{{ route('pos.update_item') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                    <input type="number" name="qty" value="{{ $item->jumlah }}" min="1"
                                           class="w-16 text-center border-gray-300 rounded-lg shadow-sm text-sm"
                                           onchange="this.form.submit()">
                                </form>
                                <form action="{{ route('pos.remove_item') }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="transaksi_detail_id" value="{{ $item->id }}">
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center">Keranjang masih kosong.</p>
                    @endforelse
                </div>

                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-md text-gray-700">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-md text-gray-700">
                        <span>Pajak & Diskon</span>
                        <span class="text-xs">(Dihitung saat checkout)</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900">
                        <span>Total</span>
                        <span>Rp {{ number_format($activeDraft->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('pos.checkout.show', $activeDraft) }}"
                       class="block w-full text-center bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200
                              @if($activeDraft->details->isEmpty()) opacity-50 pointer-events-none @endif">
                        Lanjut ke Pembayaran
                    </a>
                    <a href="{{ route('pos.new_draft') }}"
                       class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200">
                        Simpan & Tahan (Transaksi Baru)
                    </a>
                    <button 
                        type="button"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-draft-cancel-{{ $activeDraft->id }}')"
                        class="w-full text-center bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200">
                        Batalkan Transaksi Ini
                    </button>
                </div>

            </div>
        </div>

        <x-modal :name="'confirm-draft-cancel-'.$activeDraft->id" focusable>
            <form method="post" action="{{ route('pos.cancel_draft') }}" class="p-6">
                @csrf
                <input type="hidden" name="transaksi_id" value="{{ $activeDraft->id }}">
                <h2 class="text-lg font-medium text-gray-900">
                    Apakah Anda yakin ingin membatalkan draf ini?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Semua item di keranjang ini akan dihapus secara permanen.
                </p>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Jangan Batal') }}
                    </x-secondary-button>
                    <x-danger-button class="ms-3">
                        {{ __('Ya, Batalkan Draf') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>