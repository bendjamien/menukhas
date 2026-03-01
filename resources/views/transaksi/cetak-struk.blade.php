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

        /* Container for the receipt content */
        .receipt-container {
            width: 72mm; 
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
            margin: 8px 0; 
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
            padding: 2px 0;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                margin: 0;
                size: 80mm auto; 
            }
            body {
                margin: 0;
            }
            .receipt-container {
                width: 100%; 
                padding: 0 4mm; 
            }
            .no-print {
                display: none !important;
            }
        }

        .btn-print {
            display: block;
            width: 220px;
            margin: 20px auto;
            padding: 12px;
            background: #0ea5e9; 
            color: #fff;
            text-align: center;
            border-radius: 12px;
            text-decoration: none;
            font-family: sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body onload="window.print()">

    <a href="#" onclick="window.print(); return false;" class="btn-print no-print">
        Cetak Struk
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
                    <td width="30%">No. Inv</td>
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
                    <td>Customer</td>
                    <td>: {{ $transaksi->pelanggan->nama ?? 'Umum' }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <!-- Items List -->
        <table>
            @foreach ($transaksi->details as $item)
            <tr>
                <td colspan="2" class="bold" style="padding-top: 4px;">
                    {{ $item->produk->nama_produk ?? 'Item Dihapus' }}
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px; font-size: 9pt;">
                    {{ $item->jumlah }} x {{ number_format($item->harga_satuan, 0, ',', '.') }}
                </td>
                <td class="text-right" style="font-size: 9pt;">
                    {{ number_format($item->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <!-- Totals -->
        <table>
            <tr>
                <td class="text-left">Subtotal</td>
                <td class="text-right">{{ number_format($transaksi->total - $transaksi->pajak + $transaksi->diskon, 0, ',', '.') }}</td>
            </tr>
            @if($transaksi->diskon > 0)
            <tr>
                <td class="text-left">Diskon</td>
                <td class="text-right">-{{ number_format($transaksi->diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($transaksi->pajak > 0)
            <tr>
                <td class="text-left">Pajak</td>
                <td class="text-right">{{ number_format($transaksi->pajak, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <tr class="bold">
                <td class="text-left" style="padding-top: 5px; font-size: 11pt;">GRAND TOTAL</td>
                <td class="text-right" style="padding-top: 5px; font-size: 11pt;">
                    Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                </td>
            </tr>

            @if($transaksi->nominal_bayar > 0)
            <tr style="padding-top: 8px;">
                <td class="text-left">Bayar ({{ $transaksi->metode_bayar }})</td>
                <td class="text-right">{{ number_format($transaksi->nominal_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-left">Kembali</td>
                <td class="text-right">{{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        @if($transaksi->pelanggan_id && ($transaksi->poin_earned > 0 || $transaksi->poin_used > 0))
        <div class="divider"></div>
        <div class="info-block">
            <div class="bold text-center" style="margin-bottom: 3px;">INFO POIN MEMBER</div>
            <table>
                @if($transaksi->poin_used > 0)
                <tr>
                    <td>Poin Ditukar</td>
                    <td class="text-right">{{ number_format($transaksi->poin_used) }} pts</td>
                </tr>
                @endif
                @if($transaksi->poin_earned > 0)
                <tr>
                    <td>Poin Didapat</td>
                    <td class="text-right">+{{ number_format($transaksi->poin_earned) }} pts</td>
                </tr>
                @endif
                <tr>
                    <td>Total Poin</td>
                    <td class="text-right">{{ number_format($transaksi->pelanggan->poin) }} pts</td>
                </tr>
            </table>
        </div>
        @endif

        <div class="divider"></div>

        <!-- Footer -->
        <div class="text-center info-block" style="margin-top: 10px;">
            <div class="bold">TERIMA KASIH</div>
            <div>Silakan Datang Kembali</div>
            <div style="font-size: 8pt; margin-top: 8px; opacity: 0.5;">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</div>
        </div>

        <!-- Bottom Spacer for Cutter -->
        <div style="height: 15mm;"></div>

    </div>

</body>
</html>