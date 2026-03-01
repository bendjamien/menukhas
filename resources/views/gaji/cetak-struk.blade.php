<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 58mm; 
            margin: 0; 
            padding: 5px 10px; 
            font-size: 11px; 
            line-height: 1.2;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-top { border-top: 1px dashed #000; margin-top: 5px; padding-top: 5px; }
        .header { font-weight: bold; margin-bottom: 5px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .nominal-final { font-size: 14px; font-weight: bold; margin-top: 10px; text-decoration: underline; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="text-center header">
        {{ $settings['shop_name'] ?? 'MENU KHAS' }}<br>
        <span style="font-size: 10px;">SLIP GAJI KARYAWAN</span>
    </div>
    
    <div class="text-center" style="margin-bottom: 8px; font-size: 10px;">
        Periode: {{ date('F Y', mktime(0,0,0,$penggajian->bulan,10,$penggajian->tahun)) }}
    </div>

    <div style="margin-bottom: 5px;">
        Nama: {{ $penggajian->user->name }}<br>
        Tgl: {{ $penggajian->tanggal_bayar ? $penggajian->tanggal_bayar->format('d/m/Y H:i') : '-' }}<br>
        Status: SUDAH DIBAYAR
    </div>

    <div class="border-top">
        <table>
            <tr>
                <td>Gaji Pokok:</td>
                <td class="text-right">{{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lembur (+):</td>
                <td class="text-right">{{ number_format($penggajian->lembur, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kasbon (-):</td>
                <td class="text-right">{{ number_format($penggajian->potongan_kasbon, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: bold; border-top: 1px dashed #000;">
                <td style="padding-top: 5px;">TOTAL GAJI:</td>
                <td class="text-right" style="padding-top: 5px;">Rp {{ number_format($penggajian->total_diterima, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="text-center nominal-final">
        DITERIMA: Rp {{ number_format($penggajian->total_diterima, 0, ',', '.') }}
    </div>

    <div class="border-top text-center" style="margin-top: 20px;">
        Penerima,<br><br><br><br>
        ( {{ $penggajian->user->name }} )
    </div>

    <div class="text-center border-top" style="margin-top: 10px; font-size: 8px; padding-top: 5px;">
        Bukti pembayaran sah.<br>
        {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>