<x-app-layout>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pendapatan</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Pendapatan (Semua)</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalSemua, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Dari semua transaksi yang selesai.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendapatan Bulan Ini</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalBulanIni, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Dimulai dari tgl 1 bulan ini.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendapatan Hari Ini</h3>
                <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($totalHariIni, 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Untuk tanggal hari ini.</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md">
            <form action="{{ route('laporan.pendapatan') }}" method="GET">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter & Export Laporan</h3>
                
                <div class="flex flex-col md:flex-row gap-4 items-end justify-between">
                    <div class="flex gap-4 w-full md:w-auto">
                        <div class="w-full md:w-40">
                            <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select name="bulan" id="bulan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="all" {{ request('bulan') == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->isoFormat('MMMM') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full md:w-32">
                            <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select name="tahun" id="tahun" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach(range(date('Y'), 2024) as $y)
                                    <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="bg-sky-500 text-white px-4 py-2 rounded-lg hover:bg-sky-600 transition shadow-sm h-[42px]">
                                Tampilkan
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <a href="{{ route('laporan.pendapatan.pdf', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}" 
                           target="_blank"
                           class="flex items-center justify-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700 transition shadow-sm w-full md:w-auto h-[42px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak PDF
                        </a>
                        <a href="{{ route('laporan.pendapatan.excel', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}" 
                           target="_blank"
                           class="flex items-center justify-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition shadow-sm w-full md:w-auto h-[42px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    Hasil Laporan: 
                    <span class="text-sky-600">
                        {{ $bulan && $bulan != 'all' ? \Carbon\Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM') : 'Semua Bulan' }} 
                        {{ $tahun ?? date('Y') }}
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID / Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pajak</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transaksis as $transaksi)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('transaksi.show', $transaksi) }}" class="font-bold text-sky-600 hover:underline">#{{ $transaksi->id }}</a>
                                    <span class="block text-gray-600">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $transaksi->kasir->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                                    Rp {{ number_format($transaksi->diskon, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">
                                    Rp {{ number_format($transaksi->pajak, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800 text-right">
                                    Rp {{ number_format($transaksi->total, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data transaksi yang ditemukan sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transaksis->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>