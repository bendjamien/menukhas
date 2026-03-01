<x-app-layout>
    <div class="py-12 min-h-[calc(100vh-100px)] flex flex-col justify-center bg-slate-50/50">
        <div class="max-w-xl mx-auto px-4 w-full">
            
            <!-- Compact Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-black text-slate-800 tracking-tight mb-1">Tutup Kasir</h1>
                <p class="text-slate-400 text-[10px] uppercase tracking-[0.3em] font-bold">Laporan Akhir Shift</p>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                <!-- Slim Summary Bar -->
                <div class="bg-slate-900 px-6 py-6 text-white grid grid-cols-3 gap-2">
                    <div class="text-center border-r border-white/10 px-2">
                        <span class="text-[8px] text-slate-400 font-black uppercase tracking-widest block mb-1">Modal</span>
                        <span class="text-xs font-bold">{{ number_format($shift->saldo_awal, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-center border-r border-white/10 px-2">
                        <span class="text-[8px] text-slate-400 font-black uppercase tracking-widest block mb-1">Sales</span>
                        <span class="text-xs font-bold text-emerald-400">{{ number_format($totalTunaiPenjualan, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-center px-2">
                        <span class="text-[8px] text-sky-400 font-black uppercase tracking-widest block mb-1">Target</span>
                        <span class="text-xs font-black text-sky-400">{{ number_format($diharapkan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('shift.close.store') }}" method="POST">
                    @csrf
                    <div class="p-8 md:p-10 space-y-8">
                        
                        <div class="space-y-4">
                            <label for="total_tunai_aktual" class="block text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                Hitung Uang Fisik (Rp)
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                    <span class="text-xl font-black text-slate-300 transition-colors group-focus-within:text-emerald-500">Rp</span>
                                </div>
                                <input type="number" name="total_tunai_aktual" id="total_tunai_aktual" required autofocus
                                       class="w-full bg-slate-50 border-none rounded-2xl py-8 pl-16 pr-6 text-4xl font-black text-slate-800 focus:ring-4 focus:ring-emerald-500/5 text-right shadow-inner placeholder-slate-200" 
                                       placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label for="catatan" class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-2">Catatan (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="2" 
                                      class="w-full bg-slate-50 border-none rounded-xl py-4 px-5 text-sm font-medium text-slate-700 focus:ring-0 shadow-inner placeholder-slate-300"
                                      placeholder="Alasan selisih jika ada..."></textarea>
                        </div>

                        <div class="flex flex-col items-center gap-6">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Simpan & Tutup Laporan
                            </button>
                            
                            <a href="{{ route('pos.index') }}" class="text-slate-400 hover:text-slate-600 text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali ke POS
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>