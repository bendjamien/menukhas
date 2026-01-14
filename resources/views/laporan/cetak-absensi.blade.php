<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - {{ $user->name }} - {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif; /* Professional Font */
            font-size: 12pt;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            max-height: 80px;
            max-width: 80px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 11pt;
            margin: 5px 0 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11pt;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .data-table td.left {
            text-align: left;
        }
        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            width: 40%;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .sign-box {
            width: 200px;
            text-align: center;
        }
        .sign-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 100%;
            display: block;
        }

        @media print {
            @page { size: A4; margin: 2cm; }
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
            background: #f3f4f6;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Laporan (PDF)</button>
        <p style="font-size: 10pt; margin-top: 5px; color: #666;">Tekan tombol di atas atau Ctrl+P untuk menyimpan sebagai PDF.</p>
    </div>

    <div class="header">
        @if($logo)
            <img src="{{ asset('storage/'.$logo) }}" alt="Logo" class="logo">
        @endif
        <div>
            <h1>{{ $companyName }}</h1>
            <p>LAPORAN ABSENSI PEGAWAI</p>
            <p>Periode: {{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Pegawai</td>
            <td>: {{ $user->name }}</td>
            <td class="info-label">Jabatan</td>
            <td>: {{ ucfirst($user->role) }}</td>
        </tr>
        <tr>
            <td class="info-label">Jam Kerja</td>
            <td>: {{ $jamMasuk }} - {{ $jamPulang }}</td>
            <td class="info-label">ID Pegawai</td>
            <td>: #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal</th>
                <th width="15%">Masuk</th>
                <th width="15%">Pulang</th>
                <th width="15%">Status</th>
                <th width="30%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->waktu_masuk }}</td>
                <td>{{ $item->waktu_keluar ?? '-' }}</td>
                <td>{{ $item->status }}</td>
                <td class="left">
                    @if($item->status == 'Telat')
                        Terlambat {{ $item->keterlambatan }} Menit
                    @elseif($item->waktu_keluar && \Carbon\Carbon::parse($item->waktu_keluar)->lt(\Carbon\Carbon::parse($item->tanggal . ' ' . $jamPulang)))
                        Pulang Cepat
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Tidak ada data absensi bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <strong>Total Hadir:</strong>
            <span>{{ $summary['hadir'] }} Hari</span>
        </div>
        <div class="summary-row">
            <strong>Total Terlambat:</strong>
            <span>{{ $summary['telat'] }} Kali</span>
        </div>
        <div class="summary-row">
            <strong>Akumulasi Keterlambatan:</strong>
            <span>{{ $summary['total_menit_telat'] }} Menit</span>
        </div>
    </div>

    <div class="signature-section">
        <div class="sign-box">
            <p>Mengetahui,</p>
            <p><strong>Owner / Manager</strong></p>
            <span class="sign-line"></span>
        </div>
        <div class="sign-box">
            <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>Pegawai Ybs,</strong></p>
            <span class="sign-line"></span>
            <p>{{ $user->name }}</p>
        </div>
    </div>

</body>
</html>