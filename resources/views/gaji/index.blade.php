<x-app-layout>
    <div class="space-y-8" x-data="{ 
        showPayModal: false, 
        selectedGaji: null,
        userBank: '',
        userRek: '',
        userName: '',
        payUrl: ''
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Gaji Karyawan</h1>
                <p class="text-slate-500 text-sm mt-1 font-bold uppercase tracking-widest">Periode: {{ date('F', mktime(0, 0, 0, $bulan, 10)) }} {{ $tahun }}</p>
            </div>
            <div class="flex gap-3">
                <form action="{{ route('gaji.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white font-bold rounded-xl hover:bg-sky-700 transition-all text-sm shadow-lg shadow-sky-100 uppercase tracking-widest">
                        Generate Laporan Gaji
                    </button>
                </form>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex gap-4 items-end">
            <form action="{{ route('gaji.index') }}" method="GET" class="flex gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Bulan</label>
                    <select name="bulan" class="bg-slate-50 border-slate-200 rounded-xl py-2 px-4 font-bold text-slate-700">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tahun</label>
                    <select name="tahun" class="bg-slate-50 border-slate-200 rounded-xl py-2 px-4 font-bold text-slate-700">
                        @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-black text-white px-6 py-2 rounded-xl font-bold text-sm transition-all shadow-md">Tampilkan</button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold text-xs uppercase">
                        <tr>
                            <th class="px-6 py-5">Karyawan</th>
                            <th class="px-6 py-5">Gaji Pokok</th>
                            <th class="px-6 py-5">Lembur (+)</th>
                            <th class="px-6 py-5">Kasbon (-)</th>
                            <th class="px-6 py-5">Total Diterima</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($penggajians as $g)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-700">{{ $g->user->name }}</td>
                                <td class="px-6 py-4 text-slate-500 font-medium italic">Rp {{ number_format($g->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sky-600 font-bold">Rp {{ number_format($g->lembur, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-rose-600 font-bold">Rp {{ number_format($g->potongan_kasbon, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 font-black text-slate-800 text-lg">Rp {{ number_format($g->total_diterima, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $g->status_bayar == 'dibayar' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $g->status_bayar == 'dibayar' ? 'Selesai (' . $g->metode_bayar . ')' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($g->status_bayar == 'pending')
                                            <a href="{{ route('gaji.edit', $g) }}" class="p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="Edit Lembur">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <button type="button" 
                                                    @click="showPayModal = true; 
                                                            userName = '{{ $g->user->name }}';
                                                            userBank = '{{ $g->user->pengaturanGaji->bank ?? '' }}';
                                                            userRek = '{{ $g->user->pengaturanGaji->nomor_rekening ?? '' }}';
                                                            selectedGaji = 'Rp {{ number_format($g->total_diterima, 0, ',', '.') }}';
                                                            payUrl = '{{ route('gaji.bayar', $g) }}';"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20">
                                                Bayar Gaji
                                            </button>
                                        @else
                                            <button type="button" 
                                                    onclick="printReceipt('{{ route('gaji.cetak', $g) }}')"
                                                    class="bg-slate-800 hover:bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                Cetak Struk
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada laporan gaji untuk periode ini. Klik Generate.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL KONFIRMASI BAYAR GAJI -->
        <div x-show="showPayModal" 
             class="fixed inset-0 z-[99] flex items-center justify-center px-4" 
             x-cloak>
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showPayModal = false"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden"
                 x-show="showPayModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <div class="p-8 md:p-10">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800">Konfirmasi Pembayaran</h3>
                        <p class="text-slate-500 font-medium mt-1">Anda akan membayarkan gaji untuk <span class="text-slate-800 font-bold" x-text="userName"></span></p>
                    </div>

                    <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Gaji</span>
                            <span class="text-2xl font-black text-emerald-600" x-text="selectedGaji"></span>
                        </div>
                        
                        <template x-if="userBank">
                            <div class="border-t border-slate-200 pt-4 mt-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Rekening Terdaftar</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-slate-100 font-black text-xs text-sky-600" x-text="userBank"></div>
                                    <span class="text-lg font-mono font-bold text-slate-700" x-text="userRek"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <form :action="payUrl" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="metode_bayar" value="Tunai" checked class="peer sr-only">
                                    <div class="p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 group-hover:bg-slate-100">
                                        <span class="block font-black text-slate-800 uppercase tracking-widest text-xs">Tunai</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="metode_bayar" value="Transfer" class="peer sr-only">
                                    <div class="p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 group-hover:bg-slate-100">
                                        <span class="block font-black text-slate-800 uppercase tracking-widest text-xs">Transfer</span>
                                    </div>
                                </label>
                            </div>

                            <div class="flex gap-4 pt-6">
                                <button type="button" @click="showPayModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-2xl uppercase tracking-widest text-[10px]">Batal</button>
                                <button type="submit" class="flex-[2] py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 uppercase tracking-widest text-[10px] transition-all">Konfirmasi Bayar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function printReceipt(url) {
            const width = 400;
            const height = 600;
            const left = (screen.width / 2) - (width / 2);
            const top = (screen.height / 2) - (height / 2);
            
            window.open(url, 'Cetak Struk', `width=${width},height=${height},top=${top},left=${left},toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no`);
        }
    </script>
</x-app-layout>