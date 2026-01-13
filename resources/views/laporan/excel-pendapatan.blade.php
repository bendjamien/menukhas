<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="9" style="font-size: 16px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                    {{ strtoupper($settings['company_name'] ?? 'MENUKHAS') }}
                </th>
            </tr>
            <tr>
                <th colspan="9" style="text-align: center;">{{ $settings['company_address'] ?? 'Alamat Belum Diatur' }}</th>
            </tr>
            <tr>
                <th colspan="9" style="text-align: center; font-weight: bold; font-size: 14px; height: 30px; vertical-align: middle;">
                    LAPORAN PENDAPATAN
                </th>
            </tr>
            <tr>
                <th colspan="9" style="text-align: center;">Periode: {{ $periode }}</th>
            </tr>
            <tr>
                <th colspan="9"></th> <!-- Spacer -->
            </tr>
            <tr style="background-color: #f0f0f0;">
                <th style="font-weight: bold; text-align: center; width: 50px; border: 1px solid #000;">NO</th>
                <th style="font-weight: bold; text-align: center; width: 120px; border: 1px solid #000;">TANGGAL</th>
                <th style="font-weight: bold; text-align: center; width: 100px; border: 1px solid #000;">NO INVOICE</th>
                <th style="font-weight: bold; text-align: center; width: 150px; border: 1px solid #000;">KASIR</th>
                <th style="font-weight: bold; text-align: center; width: 150px; border: 1px solid #000;">PELANGGAN</th>
                <th style="font-weight: bold; text-align: center; width: 100px; border: 1px solid #000;">METODE</th>
                <th style="font-weight: bold; text-align: center; width: 100px; border: 1px solid #000;">DISKON</th>
                <th style="font-weight: bold; text-align: center; width: 100px; border: 1px solid #000;">PAJAK</th>
                <th style="font-weight: bold; text-align: center; width: 120px; border: 1px solid #000;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $t)
            <tr>
                <td style="text-align: center; border: 1px solid #000;">{{ $index + 1 }}</td>
                <td style="text-align: center; border: 1px solid #000;">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y H:i') }}</td>
                <td style="text-align: center; border: 1px solid #000;">#{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td style="border: 1px solid #000;">{{ $t->kasir->name ?? 'Admin' }}</td>
                <td style="border: 1px solid #000;">{{ $t->pelanggan->nama ?? 'Umum' }}</td>
                <td style="text-align: center; border: 1px solid #000;">{{ ucfirst($t->metode_bayar) }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ $t->diskon }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ $t->pajak }}</td>
                <td style="text-align: right; border: 1px solid #000;">{{ $t->total }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="8" style="font-weight: bold; text-align: right; border: 1px solid #000; background-color: #f0f0f0;">TOTAL PENDAPATAN</td>
                <td style="font-weight: bold; text-align: right; border: 1px solid #000; background-color: #f0f0f0;">{{ $totalPendapatan }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>