<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Riwayat Penggajian</h1>
            <p class="text-slate-500 text-sm mt-1 font-bold uppercase tracking-widest">Daftar Gaji yang Telah Dibayarkan</p>
        </div>

        <!-- Filter Riwayat -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap gap-4 items-end justify-between">
            <form action="{{ route('gaji.history') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Bulan (Opsional)</label>
                    <select name="bulan" class="bg-slate-50 border-slate-200 rounded-xl py-2 px-4 font-bold text-slate-700">
                        <option value="">Semua Bulan</option>
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
                        @for($y=date('Y')-2; $y<=date('Y'); $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-slate-800 hover:bg-black text-white px-6 py-2 rounded-xl font-bold text-sm transition-all shadow-md">Filter</button>
                    <a href="{{ route('gaji.history') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-2 rounded-xl font-bold text-sm transition-all">Reset</a>
                </div>
            </form>

            <a href="{{ route('gaji.history.export_pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-rose-50 text-rose-600 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs border border-rose-100 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
            </a>
        </div>

        <!-- Summary Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-emerald-600 rounded-3xl p-6 text-white shadow-xl shadow-emerald-100 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                <div class="relative z-10">
                    <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Total Gaji Dibayarkan</span>
                    <p class="text-3xl font-black mt-1 italic tracking-tight">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</p>
                    <p class="text-[10px] mt-2 font-medium opacity-70 italic">*Berdasarkan filter yang dipilih</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold text-xs uppercase">
                        <tr>
                            <th class="px-6 py-5">Periode</th>
                            <th class="px-6 py-5">Karyawan</th>
                            <th class="px-6 py-5">Gaji Bersih</th>
                            <th class="px-6 py-5">Metode</th>
                            <th class="px-6 py-5">Tgl Bayar</th>
                            <th class="px-6 py-5 text-center">Struk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($riwayats as $r)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-700">{{ date('F Y', mktime(0,0,0,$r->bulan,10,$r->tahun)) }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">{{ $r->user->name }}</td>
                                <td class="px-6 py-4 font-black text-emerald-600">Rp {{ number_format($r->total_diterima, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-sky-50 text-sky-700 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        {{ $r->metode_bayar ?: 'LAMA' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 font-medium">
                                    {{ $r->tanggal_bayar->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                            onclick="printReceipt('{{ route('gaji.cetak', $r) }}')"
                                            class="inline-flex p-2 bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat gaji yang dibayarkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $riwayats->links() }}</div>
        </div>
    </div>
</x-app-layout>