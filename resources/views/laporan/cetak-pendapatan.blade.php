<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #111;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0;
            font-size: 13px;
        }
        .meta-info {
            margin-bottom: 20px;
        }
        .meta-info h2 {
            font-size: 16px; 
            text-decoration: underline;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        th {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
            text-transform: uppercase;
        }
        td {
            font-size: 10pt;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Zebra striping */
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td {
            background-color: #ccc !important;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
            display: table; /* Hack for PDF alignment */
        }
        .footer-box {
            display: table-cell;
            width: 33%;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
            @page { margin: 1cm; size: A4; }
            body { -webkit-print-color-adjust: exact; }
        }
        .btn-print {
            background: #0284c7;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
            font-family: sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak PDF / Print</button>
    </div>

    <div class="header">
        <h1>{{ $settings['company_name'] ?? 'MenuKhas' }}</h1>
        <p>{{ $settings['company_address'] ?? 'Alamat Belum Diatur' }}</p>
        <p>Telp: {{ $settings['company_phone'] ?? '-' }} | Email: {{ $settings['company_email'] ?? '-' }}</p>
    </div>

    <div class="meta-info">
        <table style="border: none; margin-bottom: 0;">
            <tr style="background: none;">
                <td style="border: none; padding: 0; width: 60%;">
                    <h2 style="margin: 0;">LAPORAN PENDAPATAN</h2>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    Periode: <strong>{{ ($bulan && $bulan != 'all') ? \Carbon\Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM') : 'Semua Bulan' }} {{ $tahun }}</strong><br>
                    <span style="font-size: 10px; color: #555;">Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">Invoice</th>
                <th style="width: 15%;">Kasir</th>
                <th style="width: 15%;">Pelanggan</th>
                <th style="width: 10%;">Metode</th>
                <th style="width: 11%;">Pajak</th>
                <th style="width: 11%;">Diskon</th>
                <th style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $t)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/y H:i') }}</td>
                <td class="text-center">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $t->kasir->name ?? 'Admin' }}</td>
                <td>{{ $t->pelanggan->nama ?? '-' }}</td>
                <td class="text-center">{{ ucfirst($t->metode_bayar) }}</td>
                <td class="text-right">{{ number_format($t->pajak, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($t->diskon, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right">TOTAL PENDAPATAN BERSIH</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="footer-box">
            <p>Dibuat Oleh,</p>
            <br><br><br>
            <p><strong>( Admin Kasir )</strong></p>
        </div>
        <div class="footer-box">
            <!-- Spacer -->
        </div>
        <div class="footer-box">
            <p>Mengetahui,</p>
            <br><br><br>
            <p><strong>( Owner / Manajer )</strong></p>
        </div>
    </div>

</body>
</html>