<x-app-layout>
    <div class="max-w-xl mx-auto space-y-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Edit Detail Gaji</h1>
            <p class="text-slate-500 text-sm mt-1 font-bold uppercase tracking-widest">{{ $penggajian->user->name }} - {{ date('F Y', mktime(0,0,0,$penggajian->bulan,10,$penggajian->tahun)) }}</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <form action="{{ route('gaji.update', $penggajian) }}" method="POST" class="space-y-8">
                @csrf @method('PUT')
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <span class="block text-[10px] text-slate-400 font-black uppercase">Gaji Pokok</span>
                        <span class="font-bold text-slate-700">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-rose-50 p-4 rounded-2xl">
                        <span class="block text-[10px] text-rose-400 font-black uppercase">Potongan Kasbon</span>
                        <span class="font-bold text-rose-700">- Rp {{ number_format($penggajian->potongan_kasbon, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Nominal Lembur (Rp)</label>
                    <input type="number" name="lembur" value="{{ (int)$penggajian->lembur }}" required
                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-5 px-6 font-black text-sky-600 text-2xl focus:ring-8 focus:ring-sky-500/5 focus:border-sky-500 transition-all shadow-inner">
                    <p class="text-[10px] text-slate-400 mt-3 font-medium uppercase tracking-widest text-center">Total Gaji akan dikalkulasi otomatis saat disimpan.</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <a href="{{ route('gaji.index') }}" class="flex-1 text-center py-5 bg-slate-100 text-slate-500 font-black rounded-2xl uppercase tracking-widest text-xs">Batal</a>
                    <button type="submit" class="flex-[2] py-5 bg-sky-600 hover:bg-sky-700 text-white font-black rounded-2xl shadow-xl shadow-sky-500/20 transition-all uppercase tracking-widest text-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>