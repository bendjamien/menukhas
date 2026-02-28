<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .otp { font-size: 32px; font-weight: bold; color: #0284c7; letter-spacing: 5px; text-align: center; margin: 20px 0; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verifikasi Pendaftaran Member</h2>
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Terima kasih telah melakukan pendaftaran member di {{ config('app.name') }}. Gunakan kode verifikasi di bawah ini untuk menyelesaikan pendaftaran Anda:</p>
        
        <div class="otp">{{ $otp }}</div>
        
        <p>Kode ini berlaku selama <strong>10 menit</strong>. Jangan berikan kode ini kepada siapapun.</p>
        
        <div class="footer">
            Pesan ini dikirim secara otomatis oleh sistem POS {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
