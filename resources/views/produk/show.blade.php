@php
    use Milon\Barcode\DNS1D;
@endphp

<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-3xl mx-auto">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $produk->nama_produk }}</h1>
                <span class="text-lg text-gray-600 bg-gray-100 px-3 py-1 rounded-full font-medium">
                    {{ $produk->kategori->nama ?? 'N/A' }}
                </span>
            </div>
            <a href="{{ route('produk.index') }}" 
               class="mt-4 sm:mt-0 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            
            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Harga Jual</span>
                <span class="text-2xl font-bold text-sky-600">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Harga Beli (Modal)</span>
                <span class="text-lg text-gray-800">Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Stok Saat Ini</span>
                <span class="text-lg text-gray-800">{{ $produk->stok }} {{ $produk->satuan }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Kode Barcode</span>
                <span class="text-lg text-gray-800 font-mono">{{ $produk->kode_barcode ?? '-' }}</span>
                
                @if($produk->kode_barcode)
                    <div class="mt-2">
                        @php
                            $generator = new DNS1D();
                        @endphp
                        {!! $generator->getBarcodeHTML($produk->kode_barcode, 'C128', 2, 60) !!}
                    </div>
                @endif
            </div>

            <div class="flex flex-col md:col-span-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Deskripsi</span>
                <p class="text-base text-gray-800 whitespace-pre-line">{{ $produk->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
            </div>

        </div>

        <div class="border-t pt-6 mt-8 flex justify-end">
            <a href="{{ route('produk.edit', $produk) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-200">
                Edit Produk Ini
            </a>
        </div>

    </div>
</x-app-layout>