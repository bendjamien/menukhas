<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Produk Baru</h1>
            <a href="{{ route('produk.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="md:col-span-2">
                    <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk') }}" 
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                           required>
                </div>

                <div>
                    <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select name="kategori_id" id="kategori_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="kode_barcode" class="block text-sm font-medium text-gray-700">Kode Barcode (Scan / Ketik)</label>
                    <input type="text" name="kode_barcode" id="kode_barcode" value="{{ old('kode_barcode', $autoBarcode) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500 font-mono tracking-wide"
                           placeholder="Scan barcode produk..." autofocus
                           autocomplete="off" spellcheck="false"
                           onkeydown="if(event.key === 'Enter' || event.key === 'Tab') { 
                               event.preventDefault(); 
                               const input = this;
                               // Jeda lebih lama (100ms) untuk menangkap kode GS1 yang panjang
                               setTimeout(() => { lookupBarcode(input.value); }, 100) 
                           }">
                    <p class="text-xs text-gray-500 mt-1">Tekan tab atau scan langsung dari kemasan.</p>
                </div>

                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli', 0) }}" step="any"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" required>
                </div>

                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700">Harga Jual</label>
                    <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', 0) }}" step="any"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" required>
                </div>

                <div>
                    <label for="stok" class="block text-sm font-medium text-gray-700">Stok Awal</label>
                    <input type="number" name="stok" id="stok" value="{{ old('stok', 0) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>
                
                <div>
                    <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan (Mis: PCS, KG, Botol)</label>
                    <input type="text" name="satuan" id="satuan" value="{{ old('satuan', 'PCS') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" 
                              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">{{ old('deskripsi') }}</textarea>
                </div>
            
            </div> <div class="flex justify-end pt-6 mt-6 border-t">
                <button type="submit" 
                        class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        // Total Blocker for browser shortcuts sent by scanner (e.g., F1-F12, Ctrl combinations)
        document.addEventListener('keydown', function(e) {
            // Block all F-keys (F1 - F12)
            if (e.key.startsWith('F')) {
                e.preventDefault();
                return false;
            }
            // Block DevTools & Browser history/downloads (commonly triggered by fast scanners)
            if (e.ctrlKey && (e.shiftKey || e.key === 'j' || e.key === 'h' || e.key === 'l')) {
                const blockedShiftKeys = ['I', 'J', 'C', 'K', 'L'];
                if (blockedShiftKeys.includes(e.key.toUpperCase()) || e.key === 'j' || e.key === 'h' || e.key === 'l') {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // Function to auto-fill product data from barcode
        function lookupBarcode(barcode) {
            if (!barcode) return;
            
            // Clean GS1-128 Barcode: Remove parentheses like (90), (91), etc.
            let displayBarcode = barcode.trim();
            // This regex will remove (90), (91), (01), etc. but keep the alphanumeric data
            let searchBarcode = displayBarcode.replace(/\(\d+\)/g, '');
            
            // Update input value with cleaned version if it was GS1
            const barcodeInput = document.getElementById('kode_barcode');
            if (displayBarcode !== searchBarcode) {
                barcodeInput.value = searchBarcode;
            }

            if (searchBarcode.length < 3) return;

            barcodeInput.classList.add('bg-blue-50');
            barcodeInput.disabled = true;

            fetch(`/produk/check-barcode/${encodeURIComponent(searchBarcode)}`)
                .then(response => response.json())
                .then(result => {
                    barcodeInput.classList.remove('bg-blue-50');
                    barcodeInput.disabled = false;
                    
                    if (result.status === 'success') {
                        const data = result.data;
                        if (data.nama_produk) {
                            document.getElementById('nama_produk').value = data.nama_produk;
                        }
                        
                        if (result.source === 'local') {
                            alert('Produk ini SUDAH ADA di database Anda. Nama terisi otomatis.');
                            if (data.kategori_id) document.getElementById('kategori_id').value = data.kategori_id;
                            if (data.harga_beli) document.getElementById('harga_beli').value = data.harga_beli;
                            if (data.harga_jual) document.getElementById('harga_jual').value = data.harga_jual;
                            if (data.satuan) document.getElementById('satuan').value = data.satuan;
                        }
                        // If found name, move to harga_beli
                        setTimeout(() => document.getElementById('harga_beli').focus(), 100);
                    } else {
                        // IF NOT FOUND (Usually for Parts/Non-Food)
                        // Directly focus to Nama Produk so user can type it manually
                        setTimeout(() => document.getElementById('nama_produk').focus(), 100);
                    }
                })
                .catch(error => {
                    console.error('Lookup error:', error);
                    barcodeInput.classList.remove('bg-blue-50');
                    barcodeInput.disabled = false;
                    document.getElementById('nama_produk').focus();
                });
        }
    </script>
</x-app-layout>