<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
</head>
<body>
    <table>
        <!-- COMPANY HEADER -->
        <tr>
            <td colspan="7" style="font-weight: bold; font-size: 14pt; text-align: center; height: 30px;">
                {{ strtoupper($settings['company_name'] ?? 'MENUKHAS POS SYSTEM') }}
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">
                {{ $settings['company_address'] ?? 'Alamat Perusahaan' }}
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; border-bottom: 2px solid #000000;">
                Email: {{ $settings['company_email'] ?? '-' }} | Telp: {{ $settings['company_phone'] ?? '-' }}
            </td>
        </tr>
        <tr><td></td></tr>

        <!-- REPORT TITLE -->
        <tr>
            <td colspan="7" style="font-weight: bold; font-size: 12pt; text-align: center;">
                LAPORAN PENDAPATAN (INCOME STATEMENT)
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center;">
                Periode: {{ $periode }}
            </td>
        </tr>
        <tr><td></td></tr>

        <!-- DATA TABLE -->
        <thead>
            <tr>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle;">NO</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle; width: 20px;">TANGGAL</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle; width: 15px;">INVOICE</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle; width: 25px;">PELANGGAN</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle; width: 15px;">METODE</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: center; vertical-align: middle; width: 20px;">KASIR</th>
                <th style="font-weight: bold; background-color: #BFBFBF; border: 1px solid #000000; text-align: right; vertical-align: middle; width: 20px;">TOTAL (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $trx)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle;">{{ \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d H:i') }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">#{{ $trx->id }}</td>
                    <td style="border: 1px solid #000000; text-align: left; vertical-align: middle;">{{ $trx->pelanggan->nama ?? 'Umum' }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trx->metode_bayar }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $trx->kasir->name ?? '-' }}</td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $trx->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #E2EFDA;">GRAND TOTAL</td>
                <td style="font-weight: bold; text-align: right; border: 1px solid #000000; background-color: #E2EFDA;">{{ $totalPendapatan }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- SIGNATURE BLOCK -->
    <table>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" style="text-align: center;">
                {{ $kota }}, {{ date('d F Y') }}
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" style="text-align: center;">
                Pemilik
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" style="height: 60px;"></td> <!-- Space for signature -->
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">
                {{ $ownerName }}
            </td>
        </tr>
    </table>
</body>
</html>