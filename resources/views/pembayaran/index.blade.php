<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- HEADER -->
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Pembayaran</h2>
                    <p class="text-sm text-gray-500 mt-1">Pantau arus kas dan riwayat transaksi masuk.</p>
                </div>
                
                <form action="{{ route('pembayaran.index') }}" method="GET" class="w-full md:w-80">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 sm:text-sm transition duration-150" 
                               placeholder="Cari No. Invoice...">
                    </div>
                </form>
            </div>

            <!-- STATS CARDS (MONTHLY) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-lg shadow-emerald-200 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Pendapatan Bulan Ini</p>
                            <h3 class="text-2xl font-black mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Transaksi Tunai</p>
                        <h3 class="text-xl font-bold text-gray-800">{{ $transaksiTunai }} <span class="text-sm font-normal text-gray-400 ml-1">KALI</span></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-sky-50 rounded-xl text-sky-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Transaksi Digital</p>
                        <h3 class="text-xl font-bold text-gray-800">{{ $transaksiDigital }} <span class="text-sm font-normal text-gray-400 ml-1">KALI</span></h3>
                    </div>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden" id="pembayaran-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Invoice</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($pembayarans as $bayar)
                                <tr class="hover:bg-gray-50/50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-sky-600">#{{ str_pad($bayar->transaksi_id, 5, '0', STR_PAD_LEFT) }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">ID BAYAR: #{{ $bayar->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $bayar->transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg border
                                            {{ $bayar->metode == 'Tunai' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-sky-50 text-sky-700 border-sky-100' }}">
                                            {{ $bayar->metode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($bayar->created_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-black text-gray-900">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button onclick="printStruk('{{ route('transaksi.cetak_struk', $bayar->transaksi_id) }}')" 
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-200 transition">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            Struk
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-lg font-medium">Belum ada data pembayaran.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pembayarans->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $pembayarans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hidden Iframe for Silent Printing -->
    <iframe id="strukFrame" src="" style="position: absolute; width: 0; height: 0; border: 0; visibility: hidden;"></iframe>

    <script>
        function printStruk(url) {
            const iframe = document.getElementById('strukFrame');
            iframe.src = 'about:blank'; 
            setTimeout(() => {
                iframe.src = url;
            }, 50);
        }
    </script>
</x-app-layout>