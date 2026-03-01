<x-app-layout>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Tutup Kasir</h1>
                <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Laporan Akhir Shift & Hitung Uang Laci</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm shadow-sm">
                    Kembali ke POS
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Ringkasan Info -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-slate-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl shadow-slate-200 relative overflow-hidden">
                    <div class="absolute right-0 top-0 p-8 opacity-10">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                    </div>

                    <div class="relative z-10">
                        <h2 class="text-2xl font-black uppercase tracking-tight italic mb-8 text-sky-400">Ringkasan Shift</h2>
                        
                        <div class="space-y-8">
                            <div class="flex justify-between items-end border-b border-white/10 pb-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block mb-1">Modal Awal</span>
                                    <span class="text-lg font-mono font-bold italic">Rp {{ number_format($shift->saldo_awal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-end border-b border-white/10 pb-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block mb-1">Total Tunai (Penjualan)</span>
                                    <span class="text-lg font-mono font-bold italic text-emerald-400">+ Rp {{ number_format($totalTunaiPenjualan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="pt-4">
                                <span class="text-[10px] text-sky-400 font-black uppercase tracking-[0.2em] block mb-2">Harus Ada di Laci</span>
                                <div class="text-5xl font-black italic tracking-tighter">
                                    <span class="text-2xl not-italic font-medium text-slate-500 mr-1">Rp</span>{{ number_format($diharapkan, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 p-6 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-sm">
                        <div class="flex gap-4">
                            <svg class="w-6 h-6 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <p class="text-xs leading-relaxed text-slate-300 font-medium">
                                <strong class="text-white uppercase tracking-widest block mb-1 text-[10px]">Peringatan Keamanan</strong>
                                Mohon hitung uang fisik di laci kasir dengan teliti. Selisih yang terjadi akan dicatat permanen dalam riwayat shift Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Form Input -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-12">
                    <form action="{{ route('shift.close.store') }}" method="POST" class="space-y-10">
                        @csrf
                        <div>
                            <label for="total_tunai_aktual" class="block text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6 px-1">
                                Masukkan Total Uang Fisik Saat Ini
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                    <span class="text-3xl font-black text-slate-300 transition-colors group-focus-within:text-emerald-500">Rp</span>
                                </div>
                                <input type="number" name="total_tunai_aktual" id="total_tunai_aktual" required autofocus
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-[2.5rem] py-10 pl-24 pr-10 text-5xl font-black text-slate-800 focus:ring-8 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all shadow-inner text-right" 
                                       placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label for="catatan" class="block text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-4 px-1">Catatan Shift (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="4" 
                                      class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl py-5 px-6 text-base font-medium text-slate-700 focus:ring-8 focus:ring-sky-500/5 focus:border-sky-500 transition-all shadow-inner placeholder-slate-300"
                                      placeholder="Misal: Pecahan uang koin tidak cukup, atau ada refund manual..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-6 rounded-3xl shadow-xl shadow-emerald-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4 uppercase tracking-widest text-base">
                            Tutup & Simpan Laporan Shift
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>