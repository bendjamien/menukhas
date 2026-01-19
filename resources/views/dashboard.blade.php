<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- 1. STATS CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Pendapatan Hari Ini -->
                <div class="bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl p-6 shadow-lg shadow-blue-200 text-white transform hover:scale-105 transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium mb-1">Pendapatan Hari Ini</p>
                            <h3 class="text-3xl font-bold">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-indigo-100">
                        <span class="bg-white/20 px-1.5 py-0.5 rounded mr-1">Today</span>
                        <span>Update Realtime</span>
                    </div>
                </div>

                <!-- Card 2: Transaksi -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Transaksi Selesai</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $jumlahTransaksiHariIni }}</h3>
                        </div>
                        <div class="bg-orange-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Hari ini
                    </div>
                </div>

                <!-- Card 3: Produk -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Produk</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $jumlahProduk }}</h3>
                        </div>
                        <div class="bg-purple-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Menu aktif di database
                    </div>
                </div>

                <!-- Card 4: Pelanggan -->
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500 text-sm font-medium mb-1">Total Member</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $jumlahPelanggan }}</h3>
                        </div>
                        <div class="bg-green-50 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Pelanggan terdaftar
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- 2. CHART GRAFIK PENJUALAN (Main) -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800 text-lg">Tren Pendapatan</h3>
                        <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded">7 Hari Terakhir</span>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- 3. TOP PRODUK & STOK ALERT (Sidebar) -->
                <div class="space-y-6">
                    <!-- Top Produk -->
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Menu Terlaris
                        </h3>
                        <ul class="space-y-4">
                            @forelse($produkTerlaris as $index => $item)
                                <li class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full {{ $index == 0 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-xs font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 truncate max-w-[120px]">{{ $item->nama_produk }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded">{{ $item->total_sold }} Terjual</span>
                                </li>
                            @empty
                                <li class="text-xs text-gray-400 italic text-center py-2">Belum ada data penjualan.</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Stok Menipis -->
                    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Stok Menipis
                        </h3>
                        @if($stokMenipis->isEmpty())
                            <div class="text-center py-4 bg-green-50 rounded-lg">
                                <span class="text-xs font-medium text-green-600">Aman! Stok cukup.</span>
                            </div>
                        @else
                            <ul class="space-y-3">
                                @foreach($stokMenipis as $item)
                                    <li class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 truncate max-w-[140px]">{{ $item->nama_produk }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $item->stok == 0 ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                            Sisa: {{ $item->stok }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. TABEL TRANSAKSI TERBARU -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
                    <a href="{{ route('transaksi.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">ID</th>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3">Pelanggan</th>
                                <th class="px-6 py-3">Total</th>
                                <th class="px-6 py-3">Kasir</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transaksiTerbaru as $trx)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-mono font-medium text-gray-500">#{{ $trx->id }}</td>
                                    <td class="px-6 py-4 text-gray-700">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M, H:i') }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $trx->pelanggan->nama ?? 'Umum' }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $trx->kasir->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Selesai</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada transaksi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue start
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Transparent end

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#2563eb', // Blue-600
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#ffffff',
                    fill: true,
                    tension: 0.4 // Curve smoothing
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
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'k';
                            },
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