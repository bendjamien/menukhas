<x-app-layout>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Buka Kasir</h1>
                <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Mulai Shift Baru Anda Hari Ini</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Info Panel -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-sky-600 rounded-[2rem] p-8 text-white shadow-xl shadow-sky-100 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-sm">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black mb-2 uppercase italic tracking-tight">Persiapan Shift</h3>
                        <p class="text-sky-100 text-sm leading-relaxed font-medium">
                            Sebelum mulai bertransaksi, pastikan Anda mencatat jumlah modal awal yang ada di laci kasir untuk keperluan uang kembalian.
                        </p>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-[2rem] p-8 border border-amber-100">
                    <h4 class="text-amber-800 font-black uppercase tracking-widest text-xs mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Penting Diperhatikan
                    </h4>
                    <ul class="space-y-3">
                        <li class="flex gap-3 text-xs text-amber-700 font-medium leading-relaxed">
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-1 shrink-0"></span>
                            Modal awal akan digunakan untuk menghitung selisih di akhir shift.
                        </li>
                        <li class="flex gap-3 text-xs text-amber-700 font-medium leading-relaxed">
                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full mt-1 shrink-0"></span>
                            Pastikan nominal yang diinput sesuai dengan uang fisik di laci.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 md:p-12">
                    <form action="{{ route('shift.open.store') }}" method="POST">
                        @csrf
                        <div class="max-w-xl mx-auto space-y-10">
                            <div>
                                <label for="saldo_awal" class="block text-center text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6 px-1">
                                    Masukkan Nominal Modal Awal (Rp)
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none">
                                        <span class="text-3xl font-black text-slate-300 transition-colors group-focus-within:text-sky-500">Rp</span>
                                    </div>
                                    <input type="number" name="saldo_awal" id="saldo_awal" required autofocus
                                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-[2.5rem] py-10 pl-24 pr-10 text-5xl font-black text-slate-800 focus:ring-8 focus:ring-sky-500/5 focus:border-sky-500 transition-all shadow-inner text-right" 
                                           placeholder="0">
                                </div>
                                <div class="mt-8 flex items-center justify-center gap-4 text-slate-400">
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest px-4">Uang Laci Kasir</span>
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-6 rounded-3xl shadow-xl shadow-sky-500/20 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4 uppercase tracking-widest text-base">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 003.5 11a10.003 10.003 0 006.112-9.047m6.112 9.047c0 3.517 1.009 6.799 2.753 9.571m3.44-2.04l-.054.09A10.003 10.003 0 0120.5 11a10.003 10.003 0 01-6.112-9.047M12 11a10.003 10.003 0 016.112-9.047M12 11c0-3.517 1.009-6.799 2.753-9.571M12 11c0-3.517-1.009-6.799-2.753-9.571"></path></svg>
                                Buka Shift Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>