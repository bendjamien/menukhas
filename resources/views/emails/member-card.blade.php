<!DOCTYPE html>
<html>
<head>
    <title>Kartu Member Digital</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .card { 
            border: 1px solid #ddd; 
            border-radius: 12px; 
            padding: 20px; 
            max-width: 400px; 
            margin: 20px auto; 
            background: #f9f9f9;
            text-align: center;
        }
        .header { font-weight: bold; font-size: 1.2em; color: #0284c7; }
        .barcode { margin: 20px 0; }
        .footer { font-size: 0.8em; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">{{ config('app.name') }} MEMBER</div>
        <p>Halo, <strong>{{ $pelanggan->nama }}</strong>!</p>
        <p>Berikut adalah kartu member digital Anda. Tunjukkan barcode ini kepada kasir saat bertransaksi untuk mendapatkan poin.</p>
        
        <div class="barcode">
            <!-- Gunakan embedData agar gambar muncul di Gmail/Outlook -->
            @php
                $generator = new Milon\Barcode\DNS1D();
                // Ambil data PNG mentah (raw)
                $barcodeRaw = base64_decode($generator->getBarcodePNG($pelanggan->kode_member, 'C128', 2, 60));
            @endphp
            
            <img src="{{ $message->embedData($barcodeRaw, 'barcode.png', 'image/png') }}" alt="Barcode Member">
            
            <div style="font-family: monospace; font-weight: bold; font-size: 1.2em; letter-spacing: 2px; margin-top: 5px;">
                {{ $pelanggan->kode_member }}
            </div>
        </div>

        <p>Level: <strong>{{ $pelanggan->member_level }}</strong><br>
        Poin Saat Ini: <strong>{{ number_format($pelanggan->poin) }}</strong></p>

        <div class="footer">
            Terima kasih telah menjadi pelanggan setia kami.
        </div>
    </div>
</body>
</html>
