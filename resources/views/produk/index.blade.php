@php
    // Kita panggil library-nya untuk barcode
    use Milon\Barcode\DNS1D;
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Data Produk</h1>
            
            @if(Auth::user()->role == 'admin')
                <a href="{{ route('produk.create') }}" 
                   class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    + Tambah Produk
                </a>
            @endif
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md">
            <form action="{{ route('produk.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Cari (Nama, Barcode, atau Kategori)
                        </label>
                        <input type="search" name="search" id="search" value="{{ $search ?? '' }}" 
                               placeholder="Ketik pencarian..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="w-full py-2 px-4 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition h-fit">
                            Cari
                        </button>
                        <a href="{{ route('produk.index') }}" 
                           class="w-full text-center py-2 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition h-fit">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barcode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($produks as $produk)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $produk->nama_produk }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $produk->kategori->nama ?? 'N/A' }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if($produk->kode_barcode)
                                        @php
                                            $generator = new DNS1D();
                                        @endphp
                                        {!! $generator->getBarcodeHTML($produk->kode_barcode, 'C128', 1.5, 50) !!}
                                        <span class="font-mono text-xs block mt-1">{{ $produk->kode_barcode }}</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    Rp {{ number_format($produk->harga_jual, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $produk->stok }} <span class="text-xs text-gray-500 font-normal">{{ $produk->satuan }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <a href="{{ route('produk.show', $produk) }}" class="text-sky-600 hover:text-sky-800 px-2">Detail</a>
                                    
                                    @if(Auth::user()->role == 'admin')
                                        <a href="{{ route('produk.edit', $produk) }}" class="text-yellow-600 hover:text-yellow-800 px-2">Edit</a>
                                        
                                        <button type="button" 
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-produk-deletion-{{ $produk->id }}')"
                                                class="text-red-600 hover:text-red-800 px-2">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data produk yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($produks->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $produks->links() }}
                </div>
            @endif
        </div>
    </div>

    @foreach ($produks as $produk)
        @if(Auth::user()->role == 'admin')
            <x-modal :name="'confirm-produk-deletion-'.$produk->id" focusable>
                <form method="post" action="{{ route('produk.destroy', $produk) }}" class="p-6">
                    @csrf
                    @method('delete')
                    <h2 class="text-lg font-medium text-gray-900">
                        Apakah Anda yakin ingin menghapus produk ini?
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Anda akan menghapus: <strong>{{ $produk->nama_produk }}</strong>. <br>
                        Data riwayat penjualan dan stok log untuk produk ini juga akan terhapus.
                    </p>
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Batal') }}
                        </x-secondary-button>
                        <x-danger-button class="ms-3">
                            {{ __('Ya, Hapus Produk') }}
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>