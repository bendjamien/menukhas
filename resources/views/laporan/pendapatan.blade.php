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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Laporan Rinci</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="w-1/2 py-2 px-4 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition h-fit">
                            Filter
                        </button>
                        <a href="{{ route('laporan.pendapatan') }}" 
                           class="w-1/2 text-center py-2 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition h-fit">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">
                    Hasil Filter Laporan
                </h3>
                @if($startDate || $endDate)
                    <p class="text-sm text-gray-600">
                        Menampilkan **{{ $jumlahFiltered }} transaksi** dengan total pendapatan **Rp {{ number_format($totalFiltered, 2, ',', '.') }}**
                    </p>
                @else
                    <p class="text-sm text-gray-600">Menampilkan semua transaksi selesai (terbaru di bawah).</p>
                @endif
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