<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER & ACTIONS -->
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Pendapatan</h2>
                    <p class="text-sm text-gray-500 mt-1">Analisa performa keuangan dan arus kas masuk.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('laporan.pendapatan.pdf', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-red-700 focus:ring ring-red-300 transition shadow-lg shadow-red-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Export PDF
                    </a>
                    <a href="{{ route('laporan.pendapatan.excel', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-green-700 focus:ring ring-green-300 transition shadow-lg shadow-green-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Filtered -->
                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl shadow-lg shadow-indigo-200 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider mb-1">Total Pendapatan (Terfilter)</p>
                        <h3 class="text-3xl font-black">Rp {{ number_format($totalFiltered, 0, ',', '.') }}</h3>
                        <p class="text-xs text-indigo-200 mt-2">{{ $jumlahFiltered }} Transaksi</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-10 transform translate-y-1/4 translate-x-1/4">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                </div>

                <!-- Hari Ini -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pendapatan Hari Ini</p>
                    <div class="flex items-center gap-2">
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</h3>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">Today</span>
                    </div>
                </div>

                <!-- Bulan Ini -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pendapatan Bulan Ini</p>
                    <div class="flex items-center gap-2">
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</h3>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-full">MTD</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- CHART SECTION -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800">Tren Pendapatan</h3>
                        <span class="text-xs text-gray-400 font-medium">Sesuai Filter Periode</span>
                    </div>
                    <div class="relative h-80 w-full">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                <!-- FILTER SECTION -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Filter Laporan</h3>
                    <form action="{{ route('laporan.pendapatan') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bulan</label>
                            <select name="bulan" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="all" {{ $bulan == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun</label>
                            <select name="tahun" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                @foreach(range(date('Y'), 2024) as $y)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-lg transition shadow-md">
                            Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- TABLE -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Rincian Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($transaksis as $trx)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                        #{{ $trx->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $trx->pelanggan->nama ?? 'Umum' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $trx->metode_bayar == 'Tunai' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $trx->metode_bayar ?? 'Tunai' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-gray-800">
                                        Rp {{ number_format($trx->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                        Tidak ada data transaksi untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $transaksis->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.6)'); // Indigo
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)'); 

        new Chart(ctx, {
            type: 'bar', // Bar chart lebih cocok untuk perbandingan harian
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($dailyTotals) !!},
                    backgroundColor: '#4f46e5',
                    borderRadius: 4,
                    hoverBackgroundColor: '#4338ca'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) { return 'Rp ' + (value/1000) + 'k'; },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    </script>
</x-app-layout>