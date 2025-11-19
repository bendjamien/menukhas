<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Catat Stok Masuk</h1>
            <a href="{{ route('stok_log.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Laporan</a>
        </div>

        <form action="{{ route('stok_log.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="produk_id" class="block text-sm font-medium text-gray-700">Produk</label>
                <select id="produk_id" name="produk_id"
                        class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                        required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($produks as $produk)
                        <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                            {{ $produk->nama_produk }} (Stok saat ini: {{ $produk->stok }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah Masuk</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" min="1"
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                       placeholder="Contoh: 100" required>
            </div>

            <div>
                <label for="sumber" class="block text-sm font-medium text-gray-700">Sumber Stok</label>
                <input type="text" name="sumber" id="sumber" value="{{ old('sumber') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                       placeholder="Contoh: Pembelian dari Supplier A" required>
            </div>

            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="3" 
                          class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Simpan Stok Masuk
                </button>
            </div>
        </form>
    </div>
</x-app-layout>