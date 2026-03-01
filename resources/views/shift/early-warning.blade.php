<x-app-layout>
    <div class="h-[calc(100vh-100px)] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full text-center">
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 p-10">
                <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight mb-2">Belum Waktunya Pulang</h2>
                <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                    Jadwal pulang Anda adalah pukul <span class="font-black text-gray-800">{{ $batasWaktuPulang->format('H:i') }}</span>. 
                    Saat ini masih pukul <span class="font-black text-sky-600">{{ now()->format('H:i') }}</span>.
                </p>

                <div class="space-y-3">
                    <a href="{{ route('pos.index') }}" class="w-full block py-4 bg-sky-600 hover:bg-sky-700 text-white font-black rounded-2xl shadow-xl shadow-sky-100 transition-all uppercase tracking-widest text-xs">
                        Lanjut Bertugas
                    </a>
                    
                    <div class="pt-4 border-t border-gray-50 mt-6">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-3">Kondisi Darurat?</p>
                        <a href="{{ route('shift.close.index', ['emergency' => 1]) }}" class="text-rose-500 hover:text-rose-700 font-black text-xs uppercase tracking-widest underline decoration-2 underline-offset-4">
                            Tutup Shift Sekarang (Paksa)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
