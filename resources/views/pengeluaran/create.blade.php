<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Catat Pengeluaran</h1>
                <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Input Biaya Operasional Baru</p>
            </div>
            <a href="{{ route('pengeluaran.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Batal
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <form action="{{ route('pengeluaran.store') }}" method="POST" class="p-8 md:p-12 space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Tanggal -->
                    <div class="space-y-2">
                        <label for="tanggal" class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal Pengeluaran</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}" required
                               class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 text-slate-700 font-bold focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all">
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label for="kategori" class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Kategori Biaya</label>
                        <select name="kategori" id="kategori" required
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 text-slate-700 font-bold focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all">
                            @foreach($kategoris as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nominal -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="nominal" class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1 text-center">Nominal (Rp)</label>
                        <div class="relative group max-w-lg mx-auto">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-2xl font-black text-slate-300 group-focus-within:text-rose-500 transition-colors">Rp</span>
                            </div>
                            <input type="number" name="nominal" id="nominal" required placeholder="0"
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl py-8 pl-20 pr-8 text-4xl font-black text-slate-800 focus:ring-8 focus:ring-rose-500/5 focus:border-rose-500 transition-all text-right shadow-inner">
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="keterangan" class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Keterangan / Deskripsi</label>
                        <textarea name="keterangan" id="keterangan" rows="3" required placeholder="Contoh: Pembayaran listrik bulan Maret, Beli daging sapi 5kg, dll..."
                                  class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 text-slate-700 font-medium focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all"></textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-slate-800 hover:bg-black text-white font-black py-5 rounded-2xl shadow-xl shadow-slate-200 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                        Simpan Catatan Pengeluaran
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>