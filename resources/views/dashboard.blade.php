<x-app-layout>
    <x-slot name="header">
        {{ __('MENUKHAS POS') }}
    </x-slot>

    <div class="py-6 space-y-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- 1. HERO SECTION & WELCOME -->
            <div class="relative overflow-hidden bg-gradient-to-r from-sky-500 to-blue-600 rounded-3xl shadow-xl shadow-sky-200 mb-8 p-8 text-white isolate">
                <!-- Decorative Patterns -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl mix-blend-overlay"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-sky-300 opacity-20 rounded-full blur-3xl mix-blend-overlay"></div>
                
                <!-- Pattern overlay -->
                <svg class="absolute inset-0 -z-10 h-full w-full stroke-white/10 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
                    <defs>
                        <pattern id="983e3e4c-de6d-4c3f-8d64-b9761d1534cc" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="-1" class="overflow-visible fill-gray-800/20">
                        <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" stroke-width="0" fill="url(#983e3e4c-de6d-4c3f-8d64-b9761d1534cc)" />
                </svg>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            </div>
                            <span class="text-sky-100 font-medium tracking-wide text-sm uppercase">Selamat Datang Kembali</span>
                        </div>
                        <h1 class="text-4xl font-extrabold tracking-tight text-white mb-2">Halo, {{ Auth::user()->name }}!</h1>
                        <p class="text-sky-100 text-lg max-w-2xl font-light leading-relaxed">
                            Siap mengelola bisnis hari ini? Pantau performa toko Anda secara realtime di sini.
                        </p>
                        
                        <!-- Quick Actions -->
                        <div class="mt-8 flex flex-wrap gap-4">
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kasir')
                                <a href="{{ route('pos.index') }}" class="group flex items-center gap-3 bg-white text-blue-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-sky-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 ring-1 ring-black/5">
                                    <div class="bg-blue-100 p-1.5 rounded-md group-hover:bg-blue-200 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    Buka Kasir (POS)
                                </a>
                            @endif
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('produk.create') }}" class="flex items-center gap-3 bg-sky-700/40 hover:bg-sky-700/60 text-white px-6 py-3 rounded-xl font-medium text-sm transition-all border border-white/20 backdrop-blur-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Produk
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Absensi Alert (Floating Right) -->
                    @if(isset($absensiHariIni) && !$absensiHariIni->waktu_keluar && $isWaktunyaPulang)
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl max-w-sm w-full animate-pulse">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">WAKTU PULANG</span>
                                <span class="text-xs text-blue-100">{{ $jamPulangSetting }} WIB</span>
                            </div>
                            <p class="font-semibold text-sm mb-3">Jam kerja selesai. Jangan lupa kunci toko!</p>
                            <form id="clockOutForm" action="{{ route('absensi.clock_out') }}" method="POST">
                                @csrf
                                <input type="hidden" name="pin" id="clockOutPin">
                                <button type="button" onclick="confirmClockOut()" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 rounded-lg text-sm transition shadow-lg">
                                    Absen & Pulang Sekarang
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. MAIN STATS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Card: Pendapatan -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-blue-100 transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="relative z-10 flex-1">
                            <p class="text-gray-500 font-bold text-xs uppercase tracking-wider mb-2">Pendapatan Hari Ini</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</h3>
                            <div class="mt-4 flex items-center text-[10px] font-black text-green-600 bg-green-50 w-max px-2 py-1 rounded-lg border border-green-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                DATA REALTIME
                            </div>
                        </div>
                        <div class="relative z-10 text-blue-600 bg-blue-50 p-3 rounded-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card: Transaksi -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-orange-100 transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="relative z-10 flex-1">
                            <p class="text-gray-500 font-bold text-xs uppercase tracking-wider mb-2">Total Transaksi</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none">{{ $jumlahTransaksiHariIni }}</h3>
                            <div class="mt-4 flex items-center text-[10px] font-black text-orange-600 bg-orange-50 w-max px-2 py-1 rounded-lg border border-orange-100">
                                PESANAN HARI INI
                            </div>
                        </div>
                        <div class="relative z-10 text-orange-600 bg-orange-50 p-3 rounded-2xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card: Produk -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-purple-100 transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="relative z-10 flex-1">
                            <p class="text-gray-500 font-bold text-xs uppercase tracking-wider mb-2">Menu/Produk</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none">{{ $jumlahProduk }}</h3>
                            <div class="mt-4 flex items-center text-[10px] font-black text-purple-600 bg-purple-50 w-max px-2 py-1 rounded-lg border border-purple-100">
                                DATABASE AKTIF
                            </div>
                        </div>
                        <div class="relative z-10 text-purple-600 bg-purple-50 p-3 rounded-2xl group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Card: Pelanggan -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xl shadow-gray-200/50 hover:shadow-teal-100 transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start">
                        <div class="relative z-10 flex-1">
                            <p class="text-gray-500 font-bold text-xs uppercase tracking-wider mb-2">Total Member</p>
                            <h3 class="text-2xl font-black text-gray-800 leading-none">{{ $jumlahPelanggan }}</h3>
                            <div class="mt-4 flex items-center text-[10px] font-black text-teal-600 bg-teal-50 w-max px-2 py-1 rounded-lg border border-teal-100">
                                PELANGGAN SETIA
                            </div>
                        </div>
                        <div class="relative z-10 text-teal-600 bg-teal-50 p-3 rounded-2xl group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. CONTENT SPLIT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- LEFT COLUMN (2/3) -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- CHART SECTION -->
                    <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Grafik Penjualan</h3>
                                <p class="text-xs text-gray-400">Performa 7 hari terakhir</p>
                            </div>
                            <button class="text-gray-400 hover:text-blue-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                            </button>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <!-- RECENT TRANSACTIONS -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-gray-100 border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Transaksi Terbaru</h3>
                            <a href="{{ route('transaksi.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="p-4">
                            <table class="w-full text-sm text-left border-separate border-spacing-y-2">
                                <thead class="text-xs text-gray-400 uppercase font-light">
                                    <tr>
                                        <th class="px-4 py-2">Pelanggan</th>
                                        <th class="px-4 py-2">Total</th>
                                        <th class="px-4 py-2">Waktu</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksiTerbaru as $trx)
                                        <tr class="bg-gray-50 hover:bg-white hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5 rounded-lg group">
                                            <td class="px-4 py-4 rounded-l-lg font-medium text-gray-800">
                                                <div class="flex flex-col">
                                                    <span>{{ $trx->pelanggan->nama ?? 'Umum' }}</span>
                                                    <span class="text-xs text-gray-400 font-mono">#{{ $trx->id }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 font-bold text-blue-600">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                            <td class="px-4 py-4 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($trx->tanggal)->diffForHumans() }}</td>
                                            <td class="px-4 py-4 rounded-r-lg text-center">
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                                    Selesai
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Belum ada transaksi hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN (1/3) -->
                <div class="space-y-8">
                    
                    <!-- BEST SELLERS -->
                    <div class="bg-white p-6 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="bg-yellow-100 text-yellow-600 p-1.5 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                            </span>
                            Top Menu
                        </h3>
                        <div class="space-y-4">
                            @forelse($produkTerlaris as $index => $item)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-sm
                                            {{ $index == 0 ? 'bg-yellow-400 text-white ring-2 ring-yellow-100' : 
                                               ($index == 1 ? 'bg-gray-300 text-gray-600' : 
                                               ($index == 2 ? 'bg-orange-300 text-white' : 'bg-gray-100 text-gray-400')) }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="font-medium text-gray-700 group-hover:text-blue-600 transition truncate max-w-[150px]">{{ $item->nama_produk }}</span>
                                    </div>
                                    <span class="text-xs font-semibold bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-100">{{ $item->total_sold }} Sold</span>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-gray-400 text-sm">Belum ada data.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- STOCK ALERTS -->
                    <div class="bg-gradient-to-b from-white to-red-50 p-6 rounded-3xl shadow-xl shadow-red-50 border border-red-100">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="bg-red-100 text-red-600 p-1.5 rounded-lg animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </span>
                            Stok Menipis
                        </h3>
                        @if($stokMenipis->isEmpty())
                            <div class="flex flex-col items-center justify-center py-6 text-center">
                                <div class="bg-green-100 text-green-600 rounded-full p-3 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-sm font-medium text-gray-600">Semua stok aman!</span>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($stokMenipis as $item)
                                    <div class="bg-white p-3 rounded-xl border border-red-100 flex justify-between items-center shadow-sm">
                                        <span class="text-sm text-gray-700 font-medium truncate w-32">{{ $item->nama_produk }}</span>
                                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-600">
                                            Sisa: {{ $item->stok }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT (CHART & CLOCK OUT) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Chart Configuration
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)'); 
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)'); 

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#2563eb', // Blue 600
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f1f5f9', drawBorder: false },
                        ticks: {
                            callback: function(value) { return 'Rp ' + (value/1000) + 'k'; },
                            color: '#64748b', font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    }
                }
            }
        });

        // 2. Clock Out Logic (SweetAlert)
        function confirmClockOut() {
            Swal.fire({
                title: 'Konfirmasi Pulang',
                text: "Masukkan PIN Anda untuk konfirmasi absen pulang.",
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'PIN (6 Digit)',
                    maxlength: 6,
                    inputmode: 'numeric'
                },
                icon: 'question',
                iconColor: '#3b82f6',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Absen Pulang',
                cancelButtonText: 'Batal',
                background: '#fff',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2',
                    cancelButton: 'rounded-lg px-4 py-2'
                },
                preConfirm: (pin) => {
                    if (!pin) { Swal.showValidationMessage('PIN wajib diisi!') }
                    return pin
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clockOutPin').value = result.value;
                    document.getElementById('clockOutForm').submit();
                }
            })
        }
    </script>
</x-app-layout>