<table>
    <thead>
        <tr>
            <th colspan="4" style="font-weight: bold; text-align: center;">LAPORAN PENGELUARAN</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Periode: {{ $startDate }} s/d {{ $endDate }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="background-color: #f2f2f2; border: 1px solid #000; font-weight: bold;">Tanggal</th>
            <th style="background-color: #f2f2f2; border: 1px solid #000; font-weight: bold;">Kategori</th>
            <th style="background-color: #f2f2f2; border: 1px solid #000; font-weight: bold;">Keterangan</th>
            <th style="background-color: #f2f2f2; border: 1px solid #000; font-weight: bold;">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pengeluarans as $p)
        <tr>
            <td style="border: 1px solid #000;">{{ $p->tanggal->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000;">{{ $p->kategori }}</td>
            <td style="border: 1px solid #000;">{{ $p->keterangan }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ $p->nominal }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="font-weight: bold; text-align: right; border: 1px solid #000;">TOTAL</td>
            <td style="font-weight: bold; text-align: right; border: 1px solid #000;">{{ $pengeluarans->sum('nominal') }}</td>
        </tr>
    </tfoot>
</table>