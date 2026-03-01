<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Kasbon</title>
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
        .header { font-weight: bold; margin-bottom: 10px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .nominal { font-size: 16px; font-weight: bold; margin: 10px 0; border: 1px solid #000; padding: 5px; }
        .footer { margin-top: 20px; font-size: 9px; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="text-center header">
        {{ $settings['shop_name'] ?? 'MENU KHAS' }}<br>
        <span style="font-size: 10px;">BUKTI KASBON KARYAWAN</span>
    </div>
    
    <div style="margin-bottom: 5px;">
        ID: #KB-{{ $kasbon->id }}<br>
        Tgl: {{ $kasbon->tanggal->format('d/m/Y') }}<br>
        Staf: {{ $kasbon->user->name }}
    </div>

    <div class="border-top text-center">
        NOMINAL AMBIL:
        <div class="nominal">Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</div>
    </div>

    @if($kasbon->keterangan)
        <div style="font-style: italic; margin-bottom: 10px;">Ket: {{ $kasbon->keterangan }}</div>
    @endif

    <div class="border-top">
        <table>
            <tr>
                <td>Gaji Pokok:</td>
                <td class="text-right">{{ number_format($kasbon->user->pengaturanGaji->gaji_pokok ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Kasbon*:</td>
                <td class="text-right">{{ number_format($totalKasbonBulanIni, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: bold; font-size: 12px;">
                <td style="padding-top: 5px;">SISA GAJI:</td>
                <td class="text-right" style="padding-top: 5px;">Rp {{ number_format($sisaGaji, 0, ',', '.') }}</td>
            </tr>
        </table>
        <p style="font-size: 8px; margin-top: 8px; font-style: italic;">*Total kasbon pending bulan ini yang akan memotong gaji.</p>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        Penerima,<br><br><br><br>
        ( {{ $kasbon->user->name }} )
    </div>

    <div class="text-center footer border-top">
        Simpan struk ini sebagai bukti.<br>
        {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>