<x-app-layout>
    <div class="space-y-8">
        
        <!-- HEADER & ACTIONS -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Laporan Pendapatan</h1>
                <p class="text-slate-500 text-sm mt-1">Analisa arus kas dan performa penjualan.</p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('laporan.pendapatan.pdf', request()->query()) }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2.5 bg-slate-800 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-slate-900 transition shadow-lg shadow-slate-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    PDF
                </a>
                <a href="{{ route('laporan.pendapatan.excel', request()->query()) }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Excel
                </a>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Filtered -->
            <div class="bg-gradient-to-r from-sky-600 to-blue-700 p-6 rounded-2xl shadow-xl shadow-sky-200 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-sky-100 text-[10px] font-bold uppercase tracking-widest mb-1">Total Pendapatan (Periode Ini)</p>
                    <h3 class="text-3xl font-black tracking-tight">Rp {{ number_format($totalFiltered, 0, ',', '.') }}</h3>
                    <div class="mt-4 inline-flex items-center px-2.5 py-1 rounded-lg bg-white/20 text-xs font-bold backdrop-blur-sm">
                        {{ $jumlahFiltered }} Transaksi
                    </div>
                </div>
                <div class="absolute right-0 bottom-0 opacity-10 transform translate-y-1/4 translate-x-1/4 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                </div>
            </div>

            <!-- Hari Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-green-100 rounded-lg text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Hari Ini</p>
                </div>
                <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</h3>
            </div>

            <!-- Bulan Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Bulan Ini</p>
                </div>
                <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- CHART SECTION -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800">Grafik Penjualan</h3>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-1 rounded font-bold uppercase">Harian</span>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            <!-- FILTER SECTION -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-fit">
                <h3 class="font-bold text-slate-800 mb-6">Filter Periode</h3>
                <form action="{{ route('laporan.pendapatan') }}" method="GET" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Bulan</label>
                        <select name="bulan" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 text-sm font-medium text-slate-700 py-3">
                            <option value="all" {{ $bulan == 'all' ? 'selected' : '' }}>Semua Bulan</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Tahun</label>
                        <select name="tahun" class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 text-sm font-medium text-slate-700 py-3">
                            @foreach(range(date('Y'), 2024) as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-sky-200 transform active:scale-95 duration-200">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Rincian Transaksi</h3>
                <span class="text-xs font-medium text-slate-500">{{ $transaksis->count() }} Data ditampilkan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($transaksis as $trx)
                            <tr class="hover:bg-sky-50/30 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-sky-600">
                                    #{{ $trx->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                                    {{ $trx->pelanggan->nama ?? 'Umum' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide border {{ $trx->metode_bayar == 'Tunai' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-blue-50 text-blue-600 border-blue-100' }}">
                                        {{ $trx->metode_bayar ?? 'Tunai' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-slate-800">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-sm font-medium">Tidak ada data transaksi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transaksis->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('incomeChart').getContext('2d');
        
        // Modern Gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, '#0ea5e9'); // Sky-500
        gradient.addColorStop(1, '#3b82f6'); // Blue-500

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($dailyTotals) !!},
                    backgroundColor: '#0ea5e9',
                    borderRadius: 6,
                    hoverBackgroundColor: '#0284c7',
                    barThickness: 'flex',
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#e2e8f0',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
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
                        grid: { borderDash: [4, 4], color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            callback: function(value) { return (value/1000) + 'k'; },
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>