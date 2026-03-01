<x-app-layout>
    <div class="py-12 min-h-[calc(100vh-100px)] flex flex-col justify-center bg-gray-50/50">
        <div class="max-w-xl mx-auto px-4 w-full">
            
            <!-- Compact Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-black text-slate-800 tracking-tight mb-1">Buka Kasir</h1>
                <p class="text-slate-400 text-[10px] uppercase tracking-[0.3em] font-bold">Mulai Sesi Jualan Baru</p>
            </div>

            <!-- Smaller Input Card -->
            <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                <form action="{{ route('shift.open.store') }}" method="POST">
                    @csrf
                    <div class="p-8 md:p-10 space-y-8">
                        
                        <div class="text-center">
                            <div class="w-14 h-14 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <label for="saldo_awal" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                Modal Awal (Rp)
                            </label>
                        </div>

                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                                <span class="text-xl font-black text-slate-300 transition-colors group-focus-within:text-sky-500">Rp</span>
                            </div>
                            <input type="number" name="saldo_awal" id="saldo_awal" required autofocus
                                   class="w-full bg-slate-50 border-none rounded-2xl py-8 pl-16 pr-6 text-4xl font-black text-slate-800 focus:ring-4 focus:ring-sky-500/5 text-right shadow-inner placeholder-slate-200" 
                                   placeholder="0">
                        </div>

                        <div class="flex flex-col items-center gap-6">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Masuk ke Kasir
                            </button>
                            
                            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>