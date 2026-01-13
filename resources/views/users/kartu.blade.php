<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ID Card - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .id-card-holder {
            width: 350px; /* Ukuran standar ID Card Portrait */
            padding: 4px;
            border-radius: 12px;
            background: linear-gradient(to bottom right, #0ea5e9, #0284c7); /* Warna Sky Blue */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .id-card {
            background-color: white;
            padding: 30px 20px;
            border-radius: 8px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #e0f2fe;
            margin: 0 auto 15px;
            object-fit: cover;
            background-color: #eee;
        }
        h2 {
            margin: 0;
            color: #1e293b;
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
        }
        p.role {
            margin: 5px 0 20px;
            color: #0ea5e9;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .qr-box {
            margin: 20px auto;
            display: flex;
            justify-content: center;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #64748b;
        }
        .logo {
            font-weight: bold;
            font-size: 16px;
            color: #334155;
            margin-bottom: 20px;
            display: block;
        }
        /* Tombol Print (Hilang saat diprint) */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        button {
            background: #0ea5e9; color: white; border: none; padding: 10px 20px; 
            border-radius: 5px; cursor: pointer; font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        button:hover { background: #0284c7; }

        @media print {
            body { background: white; height: auto; display: block; }
            .id-card-holder { border: 1px solid #ddd; box-shadow: none; margin: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="id-card-holder">
        <div class="id-card">
            <span class="logo">MENUKHAS POS</span>
            
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0ea5e9&color=fff&size=200" class="avatar">
            
            <h2>{{ $user->name }}</h2>
            <p class="role">{{ $user->role }}</p>

            <div class="qr-box">
                {!! $qrCode !!}
            </div>

            <p style="font-size: 12px; color: #333;">SCAN UNTUK ABSENSI</p>
            
            <div class="footer">
                ID: {{ 'ID-' . $user->id }}<br>
                PT. MenuKhas Indonesia
            </div>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">🖨️ CETAK KARTU</button>
    </div>

</body>
</html>