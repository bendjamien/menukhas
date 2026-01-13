<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaksi->id }}</title>
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt; 
            line-height: 1.2;
            background-color: #fff;
            color: #000;
        }

        /* Container for the receipt content - ensures consistent width */
        .receipt-container {
            width: 72mm; /* Typical printable width for 80mm paper */
            margin: 0 auto;
            padding: 2mm 0;
        }

        /* Typography & Layout Helpers */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        
        .divider { 
            border-top: 1px dashed #000; 
            margin: 5px 0; 
            width: 100%;
        }
        
        .logo { 
            font-size: 14pt; 
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .info-block {
            font-size: 9pt;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        td {
            vertical-align: top;
            padding: 1px 0;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                margin: 0;
                size: 80mm auto; /* Critical for thermal printers */
            }
            body {
                margin: 0;
            }
            .receipt-container {
                width: 100%; /* Fill the page width defined by @page */
                padding: 0 2mm; /* Small side padding just in case */
            }
            .no-print {
                display: none !important;
            }
        }

        /* Button Style for Screen */
        .btn-print {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background: #2563eb; /* Sky Blue/Blue */
            color: #fff;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-family: sans-serif;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Button (Hidden when printing) -->
    <a href="#" onclick="window.print(); return false;" class="btn-print no-print">
        🖨️ Cetak Struk Sekarang
    </a>

    <div class="receipt-container">
        
        <!-- Header -->
        <div class="text-center">
            <div class="logo">{{ $settings['company_name'] ?? 'MenuKhas' }}</div>
            <div class="info-block">
                {{ $settings['company_address'] ?? 'Alamat Toko' }}<br>
                {{ $settings['company_phone'] ?? '' }}
            </div>
        </div>

        <div class="divider"></div>

        <!-- Transaction Info -->
        <div class="info-block">
            <table>
                <tr>
                    <td>No Inv</td>
                    <td>: #{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td>Tgl</td>
                    <td>: {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/y H:i') }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>: {{ $transaksi->kasir->name ?? 'Admin' }}</td>
                </tr>
                <tr>
                    <td>Plg</td>
                    <td>: {{ $transaksi->pelanggan->nama ?? 'Umum' }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <!-- Items List -->
        <table>
            @foreach ($transaksi->details as $item)
            <tr>
                <td colspan="2" style="font-weight: bold; padding-top: 4px;">
                    {{ $item->produk->nama_produk ?? 'Item Dihapus' }}
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    {{ $item->jumlah }} x {{ number_format($item->harga_satuan, 0, ',', '.') }}
                </td>
                <td class="text-right">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($transaksi->total - $transaksi->pajak + $transaksi->diskon, 0, ',', '.') }}</td>
            </tr>
            @if($transaksi->diskon > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-{{ number_format($transaksi->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($transaksi->pajak > 0)
            <tr>
                <td>Pajak</td>
                <td class="text-right">{{ number_format($transaksi->pajak, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <tr class="bold">
                <td style="padding-top: 5px; font-size: 11pt;">TOTAL</td>
                <td class="text-right" style="padding-top: 5px; font-size: 11pt;">
                    {{ number_format($transaksi->total, 0, ',', '.') }}
                </td>
            </tr>

            @if($transaksi->pembayaran)
            <tr style="padding-top: 5px;">
                <td>Bayar ({{ $transaksi->metode_bayar }})</td>
                <td class="text-right">{{ number_format($transaksi->pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">{{ number_format($transaksi->pembayaran->kembalian, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="text-center info-block" style="margin-top: 10px;">
            <div class="bold">TERIMA KASIH</div>
            <div>Silakan Datang Kembali</div>
            <div style="font-size: 8pt; margin-top: 5px;">Powered by MenuKhas</div>
        </div>

        <!-- Bottom Spacer for Cutter -->
        <div style="height: 10mm;"></div>

    </div>

</body>
</html>