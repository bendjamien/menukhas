<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Laporan Log Pembayaran</h1>
            
            <form action="{{ route('pembayaran.index') }}" method="GET" class="w-full sm:w-auto" onsubmit="return false;">
                <div class="flex rounded-md shadow-sm">
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" 
                           placeholder="Cari No. Invoice (Contoh: 123)" 
                           class="focus:ring-sky-500 focus:border-sky-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 px-4 py-2"
                           autocomplete="off"
                    >
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Hidden Iframe for Silent Printing -->
        <iframe id="strukFrame" src="" style="position: absolute; width: 0; height: 0; border: 0; visibility: hidden;"></iframe>

        <script>
            function printStruk(url) {
                const iframe = document.getElementById('strukFrame');
                iframe.src = 'about:blank'; 
                setTimeout(() => {
                    iframe.contentWindow.document.write('Loading...');
                    iframe.src = url;
                }, 50);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search-input');
                const tableContainer = document.getElementById('pembayaran-table-container');
                let searchTimeout;

                if (searchInput && tableContainer) {
                    searchInput.addEventListener('input', function() {
                        const query = this.value;
                        
                        clearTimeout(searchTimeout);
                        
                        searchTimeout = setTimeout(() => {
                            const url = "{{ route('pembayaran.index') }}?search=" + encodeURIComponent(query);
                            
                            // Update URL browser agar kalau direfresh tetap di pencarian tsb
                            window.history.pushState({path: url}, '', url);

                            // Kasih efek loading dikit (optional, misal opacity turun)
                            tableContainer.style.opacity = '0.5';

                            fetch(url)
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newContent = doc.getElementById('pembayaran-table-container').innerHTML;
                                    
                                    tableContainer.innerHTML = newContent;
                                    tableContainer.style.opacity = '1';
                                })
                                .catch(error => {
                                    console.error('Error fetching search results:', error);
                                    tableContainer.style.opacity = '1';
                                });
                        }, 300); // Delay 300ms
                    });
                }
            });
        </script>

        <div class="bg-white shadow-md rounded-lg overflow-hidden" id="pembayaran-table-container">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi</th>
                            
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Dibayar</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pembayarans as $bayar)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    #{{ $bayar->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <a href="{{ route('transaksi.show', $bayar->transaksi_id) }}" class="text-sky-600 hover:underline">
                                        #{{ str_pad($bayar->transaksi_id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $bayar->transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $bayar->metode }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $bayar->referensi ?? '-' }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-right">
                                    Rp {{ number_format($bayar->jumlah, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="printStruk('{{ route('transaksi.cetak_struk', $bayar->transaksi_id) }}')" 
                                            class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-full hover:bg-emerald-200 transition">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Struk
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada data pembayaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pembayarans->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $pembayarans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>