<x-app-layout>
    <div class="max-w-5xl mx-auto py-8 px-4" x-data="{ 
        imagePreview: '{{ $produk->image ? asset('storage/' . $produk->image) : "" }}',
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
                <h1 class="text-3xl font-black text-gray-800 tracking-tight">Edit Menu</h1>
                <p class="text-sm text-gray-500 mt-1 uppercase font-bold tracking-widest">{{ $produk->nama_produk }}</p>
            </div>
            <a href="{{ route('produk.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 rounded-2xl font-bold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                &larr; Batal & Kembali
            </a>
        </div>

        <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                                        <p class="text-[10px] font-black uppercase tracking-widest">Ganti Gambar</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" @change="handleFileChange" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        </div>
                        <p class="text-[10px] text-gray-400 leading-relaxed font-medium">Kosongkan jika tidak ingin mengubah foto.</p>
                    </div>
                </div>

                <!-- KANAN: FORM DATA -->
                <div class="flex-grow">
                    <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Nama Produk / Menu</label>
                                <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all placeholder-gray-300">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kategori</label>
                                <select name="kategori_id" required class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                                    @foreach ($kategoris as $k)
                                        <option value="{{ $k->id }}" {{ $produk->kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Kode Barcode (Readonly)</label>
                                <input type="text" value="{{ $produk->kode_barcode }}" readonly
                                       class="w-full bg-gray-100 border-none rounded-2xl py-4 px-5 font-mono text-gray-400 cursor-not-allowed uppercase tracking-widest">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Harga Beli (Modal)</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-sm">Rp</span>
                                    <input type="number" name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" required 
                                           class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-12 pr-5 font-black text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Harga Jual</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 font-bold text-sky-600 text-sm">Rp</span>
                                    <input type="number" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" required 
                                           class="w-full bg-sky-50 border-none rounded-2xl py-4 pl-12 pr-5 font-black text-sky-700 focus:ring-2 focus:ring-sky-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Stok Tersedia</label>
                                <input type="number" name="stok" value="{{ old('stok', $produk->stok) }}" 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Satuan</label>
                                <input type="text" name="satuan" value="{{ old('satuan', $produk->satuan) }}" 
                                       class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-bold text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Deskripsi Produk (Opsional)</label>
                                <textarea name="deskripsi" rows="3" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-5 font-medium text-gray-800 focus:ring-2 focus:ring-sky-500 transition-all">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 flex justify-end">
                            <button type="submit" class="w-full md:w-auto px-12 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-black rounded-2xl shadow-xl shadow-yellow-500/20 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-xs">
                                Perbarui Katalog Menu
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>
