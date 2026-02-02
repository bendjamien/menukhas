<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Stok Masuk</h1>
                <p class="text-slate-500 mt-1">Catat penambahan stok barang dari supplier atau produksi.</p>
            </div>
            <a href="{{ route('stok_log.index') }}" class="group flex items-center px-4 py-2 bg-white text-slate-600 border border-slate-200 rounded-xl hover:border-sky-300 hover:text-sky-600 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden" 
             x-data="{ 
                selectedProduk: '', 
                stokSaatIni: 0,
                satuan: '',
                produks: {{ json_encode($produks->map(function($p) { return ['id' => $p->id, 'stok' => $p->stok, 'satuan' => $p->satuan]; })) }},
                updateInfo() {
                    const p = this.produks.find(i => i.id == this.selectedProduk);
                    if(p) {
                        this.stokSaatIni = p.stok;
                        this.satuan = p.satuan || 'Pcs';
                    } else {
                        this.stokSaatIni = 0;
                        this.satuan = '';
                    }
                }
             }"
             x-init="updateInfo()">
            
            <!-- Banner Biru -->
            <div class="h-2 bg-gradient-to-r from-sky-500 to-blue-600"></div>

            <div class="p-8 md:p-10">
                <form action="{{ route('stok_log.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Kolom Kiri: Pilih Produk -->
                        <div class="space-y-6">
                            <div>
                                <label for="produk_id" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Pilih Produk</label>
                                <div class="relative">
                                    <select id="produk_id" name="produk_id" x-model="selectedProduk" @change="updateInfo()"
                                            class="block w-full pl-4 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all text-slate-700 appearance-none font-medium"
                                            required>
                                        <option value="">-- Cari Produk --</option>
                                        @foreach ($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ old('produk_id') == $produk->id ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Info Card -->
                            <div x-show="selectedProduk" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="bg-sky-50 rounded-2xl p-5 border border-sky-100 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-sky-600 font-bold uppercase mb-1">Stok Saat Ini</p>
                                    <p class="text-3xl font-black text-sky-800" x-text="stokSaatIni"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-sky-600 font-bold uppercase mb-1">Satuan</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-white text-sky-700 text-sm font-bold shadow-sm" x-text="satuan"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Input Jumlah & Sumber -->
                        <div class="space-y-6">
                            <div>
                                <label for="jumlah" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Jumlah Masuk</label>
                                <div class="relative">
                                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" min="1"
                                           class="block w-full pl-4 pr-12 py-3.5 bg-white border-2 border-slate-200 rounded-xl focus:ring-0 focus:border-sky-500 transition-all text-slate-800 font-bold text-lg placeholder-slate-300" 
                                           placeholder="0" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span class="text-slate-400 font-bold" x-text="satuan || 'Qty'"></span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="sumber" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Sumber Stok</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <input type="text" name="sumber" id="sumber" value="{{ old('sumber') }}"
                                           class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all text-slate-700 placeholder-slate-400" 
                                           placeholder="Contoh: Supplier ABC, Produksi Harian" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Full Width -->
                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Catatan Tambahan <span class="text-slate-400 font-normal normal-case">(Opsional)</span></label>
                        <textarea name="keterangan" id="keterangan" rows="3" 
                                  class="block w-full p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all text-slate-700 placeholder-slate-400 resize-none"
                                  placeholder="Tulis detail tambahan jika diperlukan...">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Footer Actions -->
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-4">
                        <a href="{{ route('stok_log.index') }}" class="px-6 py-3.5 rounded-xl text-slate-500 font-bold hover:bg-slate-100 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-8 py-3.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl shadow-lg shadow-sky-200 transform hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>