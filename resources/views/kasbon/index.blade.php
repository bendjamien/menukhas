<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Kasbon Karyawan</h1>
                <p class="text-slate-500 text-sm mt-1 font-bold uppercase tracking-widest">Kelola Pinjaman Sementara Karyawan</p>
            </div>
            <a href="{{ route('kasbon.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 transition-all text-sm shadow-lg shadow-rose-100 uppercase tracking-widest">
                Input Kasbon Baru
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold text-xs uppercase">
                    <tr>
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5">Karyawan</th>
                        <th class="px-6 py-5">Nominal</th>
                        <th class="px-6 py-5">Keterangan</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($kasbons as $k)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $k->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $k->user->name }}</td>
                            <td class="px-6 py-4 font-black text-rose-600">Rp {{ number_format($k->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-slate-500 text-sm italic">{{ $k->keterangan ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $k->status == 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $k->status == 'pending' ? 'Belum Potong Gaji' : 'Sudah Potong Gaji' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" 
                                        onclick="printReceipt('{{ route('kasbon.cetak', $k) }}')"
                                        class="inline-flex p-2 bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat kasbon.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $kasbons->links() }}</div>
        </div>
    </div>

    @if(session('print_kasbon_id'))
        <script>
            printReceipt("{{ route('kasbon.cetak', session('print_kasbon_id')) }}");
        </script>
    @endif

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