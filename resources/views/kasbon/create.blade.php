<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Input Kasbon</h1>
            <p class="text-slate-500 text-sm mt-1 font-bold uppercase tracking-widest">Pencatatan Pinjaman Karyawan</p>
        </div>

        <div class="bg-white p-8 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100">
            <form action="{{ route('kasbon.store') }}" method="POST" class="space-y-8">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Karyawan</label>
                        <select name="user_id" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 font-bold text-slate-700 focus:ring-4 focus:ring-rose-500/5 focus:border-rose-500 transition-all">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 font-bold text-slate-700">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Nominal (Rp)</label>
                            <input type="number" name="nominal" required placeholder="0" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 font-black text-rose-600 text-xl">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Keterangan (Alasan Kasbon)</label>
                        <textarea name="keterangan" rows="3" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 px-5 font-medium text-slate-600 placeholder-slate-300" placeholder="Misal: Keperluan mendesak keluarga..."></textarea>
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <a href="{{ route('kasbon.index') }}" class="flex-1 text-center py-5 bg-slate-100 text-slate-500 font-black rounded-2xl uppercase tracking-widest text-xs">Batal</a>
                    <button type="submit" class="flex-[2] py-5 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl shadow-xl shadow-rose-500/20 uppercase tracking-widest text-xs transition-all transform hover:-translate-y-1">Simpan & Cetak Struk</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>