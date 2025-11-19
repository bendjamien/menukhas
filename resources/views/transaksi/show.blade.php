<x-app-layout>
    <style>
        @media print {
            body * { visibility: hidden !important; }
            .printable-area, .printable-area * { visibility: visible !important; }
            .printable-area {
                position: absolute !important; left: 0 !important; top: 0 !important;
                width: 100% !important; max-width: 100% !important;
                box-shadow: none !important; margin: 0 !important; padding: 1.5rem !important;
                border: none !important;
            }
        }
    </style>

    <div class="max-w-4xl mx-auto mb-4">
        <div class="flex justify-between items-center">
            <a href="{{ route('transaksi.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                &larr; Kembali ke Laporan
            </a>
            <button 
                onclick="window.print()"
                class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                Cetak Struk
            </button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8 sm:p-12 mb-12 printable-area">
        
        <header class="flex justify-between items-start pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $settings['company_name'] ?? 'MenuKhas' }}</h1>
                <p class="text-sm text-gray-600">{{ $settings['company_website'] ?? '' }}</p>
                <p class="text-sm text-gray-600">{{ $settings['company_email'] ?? '' }}</p>
                <p class="text-sm text-gray-600">{{ $settings['company_phone'] ?? '' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Lokasi Bisnis</p>
                <p class="text-sm text-gray-600">{{ $settings['company_address'] ?? '' }}</p>
                <p class="text-sm text-gray-600">Pajak ID: {{ $settings['company_tax_id'] ?? '' }}</p>
            </div>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 my-8">
            <div>
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ditagihkan kepada :</h2>
                <p class="text-lg font-bold text-gray-800">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</p>
                <p class="text-sm text-gray-600">{{ $transaksi->pelanggan->alamat ?? 'Cianjur, Jawa Barat' }}</p>
                <p class="text-sm text-gray-600">{{ $transaksi->pelanggan->no_hp ?? '' }}</p>
            </div>
            <div class="md:col-span-2 grid grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <p class="text-sm text-gray-500">Nomor Invoice</p>
                    <p class="text-md font-semibold text-gray-800">#{{ $transaksi->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Invoice</p>
                    <p class="text-md font-semibold text-gray-800">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d F, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Referensi</p>
                    <p class="text-md font-semibold text-gray-800">{{ $transaksi->pembayaran->referensi ?? 'INV-' . $transaksi->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tenggat Waktu</p>
                    <p class="text-md font-semibold text-gray-800">{{ \Carbon\Carbon::parse($transaksi->tanggal)->addDays(5)->format('d F, Y') }}</p>
                </div>
            </div>
            <div class="md:col-start-3 md:row-start-1 bg-gray-50 rounded-lg p-6 text-right">
                <p class="text-sm text-gray-500 uppercase tracking-wider">Nominal Bayar (Rupiah)</p>
                <p class="text-4xl font-bold text-sky-600 mt-2">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</p>
            </div>
        </section>

        <section>
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Detail</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($transaksi->details as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-800">{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->produk->deskripsi ?? 'Detail item' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $item->jumlah }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mt-8">
            <div class="max-w-xs ml-auto text-right space-y-3">
                @php
                    $totalSebelumPajak = $transaksi->total - $transaksi->pajak;
                    $subtotal = $totalSebelumPajak + $transaksi->diskon;
                    
                    $persenPajak = 0;
                    if ($totalSebelumPajak > 0) {
                        $persenPajak = ($transaksi->pajak / $totalSebelumPajak) * 100;
                    }
                @endphp
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Subtotal</span>
                    <span class="text-md font-medium text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($transaksi->diskon > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Diskon</span>
                        <span class="text-md font-medium text-gray-800">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pajak ({{ number_format($persenPajak, 0) }}%)</span>
                    <span class="text-md font-medium text-gray-800">Rp {{ number_format($transaksi->pajak, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                    <span class="text-lg font-bold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-gray-900">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        <footer class="mt-12 pt-6 border-t border-gray-200">
            <p class="text-center text-sm text-gray-600 mb-2">
                Terimakasih Sudah Berbelanja
            </p>
            <p class="text-center text-xs text-gray-500">
                Syarat & Ketentuan: Harap membayar dalam waktu 5 hari setelah menerima faktur ini.
            </p>
        </footer>

    </div>

</x-app-layout>