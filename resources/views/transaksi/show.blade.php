<x-app-layout>
    <style>
        @media print {
            /* Sembunyikan semua elemen saat ngeprint */
            body * { visibility: hidden !important; }
            
            /* Kecuali area printable */
            .printable-area, .printable-area * { visibility: visible !important; }
            
            /* Atur posisi area printable agar pas di kertas */
            .printable-area {
                position: absolute !important; 
                left: 0 !important; 
                top: 0 !important;
                width: 100% !important; 
                max-width: 100% !important;
                box-shadow: none !important; 
                margin: 0 !important; 
                padding: 0 !important; /* Padding diatur oleh browser/halaman */
                border: none !important;
            }

            /* Paksa background color tetap muncul saat diprint */
            .bg-print-gray {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }

            /* Sembunyikan header/footer bawaan browser jika memungkinkan (opsional) */
            @page { margin: 0.5cm; }
        }
    </style>

    <div class="max-w-4xl mx-auto mb-6 mt-4 px-4 sm:px-0">
        <div class="flex justify-between items-center">
            <a href="{{ route('transaksi.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <button onclick="window.print()"
                    class="bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak PDF
            </button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8 sm:p-12 mb-12 printable-area border border-gray-100">
        
        <header class="flex justify-between items-start pb-8 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $settings['company_name'] ?? 'MenuKhas' }}</h1>
                <div class="mt-2 space-y-0.5">
                    <p class="text-sm text-gray-500">{{ $settings['company_website'] ?? 'www.menukhas.com' }}</p>
                    <p class="text-sm text-gray-500">{{ $settings['company_email'] ?? 'support@menukhas.com' }}</p>
                    <p class="text-sm text-gray-500">{{ $settings['company_phone'] ?? '+62 812 3456 7890' }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Lokasi Bisnis</p>
                <p class="text-sm text-gray-600 font-medium">{{ $settings['company_address'] ?? 'Alamat Toko Belum Diatur' }}</p>
                <p class="text-sm text-gray-500 mt-1">NPWP/Tax ID: {{ $settings['company_tax_id'] ?? '-' }}</p>
            </div>
        </header>

        <div class="flex justify-between items-start my-8 gap-8">
            
            <div class="flex-1">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ditagihkan kepada :</h2>
                <p class="text-xl font-bold text-gray-800">{{ $transaksi->pelanggan->nama ?? 'Pelanggan Umum' }}</p>
                <div class="mt-1 text-gray-600 text-sm">
                    <p>{{ $transaksi->pelanggan->alamat ?? '-' }}</p>
                    <p>{{ $transaksi->pelanggan->no_hp ?? '-' }}</p>
                </div>
            </div>

            <div class="text-right bg-gray-50 bg-print-gray rounded-xl p-6 w-auto min-w-[250px] border border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Tagihan (Rupiah)</p>
                <p class="text-4xl font-bold text-sky-600">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mb-8 py-6 border-t border-b border-gray-100">
            <div>
                <p class="text-xs text-gray-500 mb-1">Nomor Invoice</p>
                <p class="text-base font-bold text-gray-800">#{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Tanggal Invoice</p>
                <p class="text-base font-semibold text-gray-800">{{ \Carbon\Carbon::parse($transaksi->tanggal)->isoFormat('D MMMM Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Referensi / Metode</p>
                <p class="text-base font-semibold text-gray-800">{{ $transaksi->pembayaran->referensi ?? 'POS - ' . ($transaksi->metode_bayar ?? 'Tunai') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Status</p>
                <p class="text-base font-bold text-emerald-600 uppercase">{{ $transaksi->status }}</p>
            </div>
        </div>

        <div class="mb-8">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Detail</th>
                        <th class="text-center py-3 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="text-right py-3 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th class="text-right py-3 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($transaksi->details as $item)
                        <tr class="border-b border-gray-50">
                            <td class="py-4 px-2">
                                <p class="text-sm font-bold text-gray-800">{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->produk->kategori->nama ?? '' }}</p>
                            </td>
                            <td class="py-4 px-2 text-center text-sm">{{ $item->jumlah }}</td>
                            <td class="py-4 px-2 text-right text-sm">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-4 px-2 text-right text-sm font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <div class="w-full sm:w-1/2 md:w-1/3 space-y-3">
                @php
                    // Kalkulasi mundur sederhana untuk display
                    $grandTotal = $transaksi->total;
                    $pajakVal = $transaksi->pajak;
                    $diskonVal = $transaksi->diskon;
                    // Asumsi: subtotal + pajak - diskon = total
                    $subtotalDisplay = $grandTotal - $pajakVal + $diskonVal; 
                @endphp

                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-medium">Rp {{ number_format($subtotalDisplay, 0, ',', '.') }}</span>
                </div>
                
                @if ($diskonVal > 0)
                <div class="flex justify-between text-sm text-rose-500">
                    <span>Diskon</span>
                    <span class="font-medium">- Rp {{ number_format($diskonVal, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="flex justify-between text-sm text-gray-600">
                    <span>Pajak PPN</span>
                    <span class="font-medium">Rp {{ number_format($pajakVal, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200 mt-3">
                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                    <span class="text-xl font-bold text-sky-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <footer class="mt-16 pt-8 border-t border-gray-200 text-center">
            <h4 class="text-sm font-bold text-gray-800 mb-2">Terima Kasih Telah Berbelanja!</h4>
            <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
                Simpan struk ini sebagai bukti pembayaran yang sah. 
                Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan kecuali ada perjanjian sebelumnya.
            </p>
        </footer>

    </div>
</x-app-layout>