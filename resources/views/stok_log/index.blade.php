<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Laporan Stok (Kartu Stok)</h1>
            
            @if(Auth::user()->role == 'admin')
                <a href="{{ route('stok_log.create') }}" 
                   class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    + Catat Stok Masuk
                </a>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cek Stok Produk Saat Ini</h3>
            <form action="{{ route('stok_log.index') }}" method="GET">
                <label for="produk_search_id" class="block text-sm font-medium text-gray-700">Pilih Produk:</label>
                <select name="produk_search_id" id="produk_search_id"
                        class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500"
                        onchange="this.form.submit()">
                    <option value="">-- Pilih salah satu produk --</option>
                    @foreach ($produksForSearch as $produk)
                        <option value="{{ $produk->id }}" {{ $produk->id == $selectedProdukId ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </form>
            @if ($selectedProduk)
                <div class="mt-6 border-t pt-4">
                    <p class="text-sm text-gray-600">Sisa Stok Saat Ini untuk:</p>
                    <p class="text-2xl font-bold text-sky-600">{{ $selectedProduk->nama_produk }}</p>
                    <p class="text-4xl font-bold text-gray-900 mt-2">
                        {{ $selectedProduk->stok }} <span class="text-2xl font-medium text-gray-600">{{ $selectedProduk->satuan }}</span>
                    </p>
                </div>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-800 p-4 border-b">Riwayat Stok Keluar/Masuk</h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber / Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh (User)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Stok Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y, H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->produk->nama_produk ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                    @if($log->tipe == 'masuk') <span class="text-green-600">MASUK</span>
                                    @else <span class="text-red-600">KELUAR</span> @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold @if($log->tipe == 'masuk') text-green-600 @else text-red-600 @endif">
                                    {{ $log->tipe == 'masuk' ? '+' : '-' }} {{ $log->jumlah }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="font-medium">{{ $log->sumber }}</span>
                                    <span class="block text-xs text-gray-500">{{ $log->keterangan }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">
                                    {{ $log->produk->stok ?? 'N/A' }}
                                    <span class="text-xs text-gray-500 font-normal">{{ $log->produk->satuan ?? '' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada riwayat stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>