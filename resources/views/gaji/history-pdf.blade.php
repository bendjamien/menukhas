<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penggajian Karyawan</title>
    <style>
        @page {
            margin: 1cm;
        }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 10px; 
            color: #334155; 
            line-height: 1.5;
        }
        .header-container { 
            border-bottom: 3px solid #0ea5e9; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .shop-name { 
            font-size: 22px; 
            font-weight: 900; 
            color: #0f172a; 
            text-transform: uppercase; 
            margin: 0;
            letter-spacing: -0.5px;
        }
        .shop-info { 
            font-size: 10px; 
            color: #64748b; 
            margin: 2px 0; 
        }
        
        .report-title { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .report-title h2 { 
            font-size: 16px; 
            font-weight: 800; 
            color: #1e293b; 
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-period {
            font-size: 11px;
            color: #0ea5e9;
            font-weight: bold;
            margin-top: 5px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            font-size: 9px;
            color: #64748b;
        }
        
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            background-color: #fff;
        }
        table.data-table th { 
            background-color: #f8fafc; 
            border-bottom: 2px solid #e2e8f0;
            color: #475569;
            padding: 12px 8px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        table.data-table td { 
            border-bottom: 1px solid #f1f5f9; 
            padding: 10px 8px; 
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-sky { color: #0ea5e9; }
        .text-rose { color: #e11d48; }
        .text-emerald { color: #10b981; }
        
        .summary-section { 
            margin-top: 30px; 
            width: 100%;
        }
        .summary-card {
            float: right;
            width: 250px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-highlight {
            border-top: 2px solid #e2e8f0;
            margin-top: 10px;
            padding-top: 10px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 900;
        }
        
        .footer { 
            margin-top: 100px; 
            width: 100%;
        }
        .signature-wrapper {
            float: right;
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #334155;
            padding-top: 5px;
            font-weight: bold;
            color: #0f172a;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .badge-sky { background-color: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>
    <div class="header-container">
        <table width="100%">
            <tr>
                <td>
                    <h1 class="shop-name">{{ $settings['shop_name'] ?? 'MENU KHAS' }}</h1>
                    <p class="shop-info">{{ $settings['shop_address'] ?? 'Laporan Operasional Toko' }}</p>
                    <p class="shop-info">Telp: {{ $settings['shop_phone'] ?? '-' }}</p>
                </td>
                <td class="text-right" style="vertical-align: top;">
                    <div style="font-size: 14px; font-weight: 900; color: #cbd5e1;">OFFICIAL REPORT</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h2>Riwayat Penggajian Karyawan</h2>
        <div class="report-period">
            Periode: {{ $bulan ? date('F', mktime(0,0,0,$bulan,10)) : 'Seluruh Bulan' }} {{ $tahun }}
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td width="50%">Dicetak oleh: {{ Auth::user()->name }}</td>
            <td width="50%" class="text-right">Tanggal Cetak: {{ date('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Periode Gaji</th>
                <th width="25%">Karyawan</th>
                <th width="15%">Gaji Pokok</th>
                <th width="10%">Lembur</th>
                <th width="10%">Kasbon</th>
                <th width="20%" class="text-right">Total Diterima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayats as $index => $r)
            <tr>
                <td style="color: #94a3b8;">{{ $index + 1 }}</td>
                <td class="font-bold">{{ date('M Y', mktime(0,0,0,$r->bulan,10,$r->tahun)) }}</td>
                <td>
                    <div class="font-bold" style="color: #0f172a;">{{ $r->user->name }}</div>
                    <span class="badge badge-sky">{{ $r->metode_bayar }}</span>
                </td>
                <td>Rp {{ number_format($r->gaji_pokok, 0, ',', '.') }}</td>
                <td class="text-sky">+{{ number_format($r->lembur, 0, ',', '.') }}</td>
                <td class="text-rose">-{{ number_format($r->potongan_kasbon, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #0f172a; font-size: 11px;">
                    Rp {{ number_format($r->total_diterima, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <div class="summary-card">
            <div style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">
                Ringkasan Pembayaran
            </div>
            <div class="summary-item">
                <span style="color: #64748b;">Total Data:</span>
                <span class="font-bold">{{ count($riwayats) }} Transaksi</span>
            </div>
            <div class="summary-item total-highlight">
                <span>TOTAL DIBAYAR:</span>
                <span class="text-emerald">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        <div class="signature-wrapper">
            <p style="margin-bottom: 5px; color: #64748b;">Dikeluarkan pada {{ date('d/m/Y') }}</p>
            <p>Oleh Manager Operasional,</p>
            <div class="signature-line">
                {{ Auth::user()->name }}
            </div>
            <p style="font-size: 8px; color: #94a3b8; margin-top: 5px;">Digital Signed Document</p>
        </div>
    </div>
</body>
</html>