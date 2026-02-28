<x-app-layout>
    <div class="max-w-5xl mx-auto py-8 px-4" x-data="{ 
        imagePreview: null,
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.imagePreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight">Tambah Menu Baru</h1>
                <p class="text-sm text-gray-500 mt-1 uppercase font-bold tracking-widest">Inventaris & Katalog Produk</p>
            </div>
            <a href="{{ route('produk.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-2xl font-bold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                &larr; Batal & Kembali
            </a>
        </div>

        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- KIRI: UPLOAD GAMBAR -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 text-center">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Foto Produk</label>
                        
                        <div class="relative group mx-auto mb-6">
                            <div class="w-full aspect-square rounded-[2rem] bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-sky-400 group-hover:bg-sky-50">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="flex flex-col items-center text-gray-300 group-hover:text-sky-400 transition-colors">
                                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-[10px] font-black uppercase tracking-widest">Pilih Gambar</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" @change="handleFileChange" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-relaxed font-medium">Gunakan gambar resolusi tinggi (PNG/JPG) maksimal 2MB untuk hasil terbaik.</p>
                    </div>
                </div>

                <!-- KANAN: FORM DATA -->
                <div class="flex-grow">
                    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Produk / Menu</label>
                                <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" required 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all placeholder-gray-300" placeholder="Contoh: Kopi Susu Gula Aren">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kategori</label>
                                <select name="kategori_id" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($kategoris as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kode Barcode (Scan Sekarang)</label>
                                <input type="text" name="kode_barcode" id="kode_barcode" value="{{ old('kode_barcode', $autoBarcode) }}"
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-mono text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all placeholder-gray-300" 
                                       placeholder="Scan..."
                                       autofocus
                                       autocomplete="off"
                                       onkeydown="if(event.key === 'Enter' || event.key === 'Tab') { 
                                           event.preventDefault(); 
                                           const val = this.value;
                                           setTimeout(() => lookupBarcode(val), 50); 
                                       }">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Harga Beli (Modal)</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="harga_beli" value="{{ old('harga_beli', 0) }}" required 
                                           class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-12 pr-5 font-black text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Harga Jual</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-bold text-sky-600 text-sm">Rp</span>
                                    <input type="number" name="harga_jual" value="{{ old('harga_jual', 0) }}" required 
                                           class="w-full bg-sky-50 border-none rounded-2xl py-4 pl-12 pr-5 font-black text-sky-700 focus:ring-2 focus:ring-sky-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Stok Awal</label>
                                <input type="number" name="stok" value="{{ old('stok', 0) }}" 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Satuan</label>
                                <input type="text" name="satuan" value="{{ old('satuan', 'PCS') }}" 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all" placeholder="PCS / Porsi / KG">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Deskripsi Produk (Opsional)</label>
                                <textarea name="deskripsi" rows="3" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-medium text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all placeholder-gray-300" placeholder="Jelaskan detail menu ini..."></textarea>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 flex justify-end">
                            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-sky-600 hover:bg-sky-700 text-white font-black rounded-2xl shadow-xl shadow-sky-500/20 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-xs">
                                Simpan Produk & Katalog
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        // Global Barcode Collector for Product Create
        let barcodeBuffer = '';
        let barcodeTimeout = null;

        document.addEventListener('keydown', e => {
            // Block DevTools & Shortcuts
            if (e.key.startsWith('F') || (e.ctrlKey && ['I','J','C','K','L'].includes(e.key.toUpperCase()))) {
                e.preventDefault();
                return;
            }

            // Global capture logic
            if (e.key.length === 1) {
                barcodeBuffer += e.key;
                if (barcodeTimeout) clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(() => { barcodeBuffer = ''; }, 150);
            } else if (e.key === 'Enter' || e.key === 'Tab') {
                if (barcodeBuffer.length >= 3) {
                    e.preventDefault();
                    lookupBarcode(barcodeBuffer);
                    barcodeBuffer = '';
                }
            }
        });

        // Function to auto-fill product data from barcode
        async function lookupBarcode(barcode) {
            if (!barcode) return;
            const cleanBarcode = barcode.trim().replace(/\(\d+\)/g, '');
            const input = document.getElementById('kode_barcode');
            
            // Set value to input
            input.value = cleanBarcode;
            input.classList.add('bg-sky-100');

            try {
                let res = await fetch(`/produk/check-barcode/${encodeURIComponent(cleanBarcode)}`);
                let result = await res.json();
                input.classList.remove('bg-sky-100');
                
                if (result.status === 'success') {
                    const data = result.data;
                    if (data.nama_produk) {
                        document.getElementsByName('nama_produk')[0].value = data.nama_produk;
                    }
                    
                    if (result.source === 'local') {
                        Toastify({ text: 'Produk sudah ada di database.', duration: 3000, style: { background: '#0284c7' } }).showToast();
                    } else {
                        Toastify({ text: 'Data produk ditemukan!', duration: 2000, style: { background: '#10b981' } }).showToast();
                    }
                    // Focus next field (Harga Beli)
                    document.getElementsByName('harga_beli')[0].focus();
                } else {
                    // Not found, focus Nama Produk
                    document.getElementsByName('nama_produk')[0].focus();
                }
            } catch (e) { 
                input.classList.remove('bg-sky-100');
                document.getElementsByName('nama_produk')[0].focus();
            }
        }
    </script>
</x-app-layout>
