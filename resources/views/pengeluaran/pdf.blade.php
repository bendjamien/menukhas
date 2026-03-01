<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; text-transform: uppercase; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        table.data td { border: 1px solid #dee2e6; padding: 10px; vertical-align: top; }
        
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer { margin-top: 50px; }
        .signature { float: right; width: 200px; text-align: center; }
        .signature-space { height: 70px; }
        
        .total-row { background: #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $settings['shop_name'] ?? 'MENU KHAS' }}</h1>
        <p>{{ $settings['shop_address'] ?? 'Laporan Operasional Toko' }}</p>
        <p>Telepon: {{ $settings['shop_phone'] ?? '-' }}</p>
    </div>

    <div class="info">
        <h2 style="text-align: center; font-size: 16px;">LAPORAN PENGELUARAN</h2>
        <table>
            <tr>
                <td width="100">Periode</td>
                <td>: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th width="100">Kategori</th>
                <th>Keterangan</th>
                <th width="120" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluarans as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                <td>{{ $p->kategori }}</td>
                <td>{{ $p->keterangan }}</td>
                <td class="text-right">{{ number_format($p->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ date('d F Y') }}</p>
            <p>Admin / Manager,</p>
            <div class="signature-space"></div>
            <p><strong>( ____________________ )</strong></p>
        </div>
    </div>
</body>
</html>