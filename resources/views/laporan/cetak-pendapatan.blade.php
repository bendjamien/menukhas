<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #111; }
        .header p { margin: 2px 0; font-size: 10px; color: #555; }
        
        .meta { margin-bottom: 20px; }
        .meta table { width: 100%; border: none; }
        .meta td { padding: 2px; }
        .meta .label { font-weight: bold; width: 120px; }

        .summary-box { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }
        .summary-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; }
        .summary-value { font-size: 18px; color: #2563eb; font-weight: bold; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data th { background-color: #f1f5f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        table.data tr:nth-child(even) { background-color: #f9fafb; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { margin-top: 40px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; width: 200px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header">
        <h1>{{ $settings['company_name'] ?? 'MENUKHAS POS SYSTEM' }}</h1>
        <p>{{ $settings['company_address'] ?? 'Jl. Raya Bisnis No. 123, Jakarta Pusat' }}</p>
        <p>Email: {{ $settings['company_email'] ?? 'admin@menukhas.com' }} | Telp: {{ $settings['company_phone'] ?? '(021) 555-1234' }}</p>
    </div>

    <!-- META DATA -->
    <div class="meta">
        <table>
            <tr>
                <td class="label">Dokumen:</td>
                <td>LAPORAN PENDAPATAN (INCOME STATEMENT)</td>
                <td class="label">Dicetak Oleh:</td>
                <td>{{ auth()->user()->name }}</td>
            </tr>
            <tr>
                <td class="label">Periode:</td>
                <td>
                    @if($bulan && $bulan != 'all')
                        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    @else
                        Semua Bulan {{ $tahun }}
                    @endif
                </td>
                <td class="label">Tanggal Cetak:</td>
                <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- EXECUTIVE SUMMARY -->
    <div class="summary-box">
        <div class="summary-title">TOTAL PENDAPATAN PERIODE INI</div>
        <div class="summary-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>

    <!-- TABEL DATA -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th>Kasir</th>
                <th class="text-right">Total (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y H:i') }}</td>
                <td>#{{ $trx->id }}</td>
                <td>{{ $trx->pelanggan->nama ?? 'Umum' }}</td>
                <td>{{ $trx->metode_bayar }}</td>
                <td>{{ $trx->kasir->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($trx->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right" style="font-weight: bold; background-color: #f1f5f9;">GRAND TOTAL</td>
                <td class="text-right" style="font-weight: bold; background-color: #f1f5f9;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER SIGNATURE -->
    <div class="footer">
        <div class="signature-box">
            <p>{{ $kota }}, {{ date('d F Y') }}</p>
            <p>Pemilik,</p>
            <div class="signature-line"></div>
            <p style="font-weight: bold;">{{ $ownerName }}</p>
        </div>
    </div>

</body>
</html>