<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Produk</h1>
            <a href="{{ route('produk.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('produk.update', $produk) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2">
                    <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" 
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                           required>
                </div>

                <div>
                    <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="kategori_id" id="kategori_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="kode_barcode" class="block text-sm font-medium text-gray-700">Kode Barcode (Opsional)</label>
                    <input type="text" name="kode_barcode" id="kode_barcode" value="{{ old('kode_barcode', $produk->kode_barcode) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" step="any"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" required>
                </div>

                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                    <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" step="any"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" required>
                </div>

                <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700">Stok</label>
                    <input type="number" name="stok" id="stok" value="{{ old('stok', $produk->stok) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>
                
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan (Mis: PCS, KG, Botol)</label>
                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan', $produk->satuan) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" 
                              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>
            
            </div> <div class="flex justify-end pt-6 mt-6 border-t">
                <button type="submit" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Update Produk
                </button>
            </div>
        </form>
    </div>
</x-app-layout>