<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADER & ACTIONS -->
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Stok</h2>
                    <p class="text-sm text-gray-500 mt-1">Lacak pergerakan inventaris dan cek ketersediaan barang.</p>
                </div>
                <a href="{{ route('stok_log.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring ring-blue-300 transition ease-in-out duration-150 shadow-lg shadow-blueoke -200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Barang Masuk
                </a>
            </div>

            <!-- JIKA PRODUK DIPILIH: TAMPILKAN KARTU DETAIL (CEK STOK) -->
            @if($selectedProduk)
                <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl border border-slate-700 relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                        
                        <!-- Info Produk -->
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center text-white backdrop-blur-sm border border-white/20">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-mono mb-1">{{ $selectedProduk->kode_barcode ?? 'NO-BARCODE' }}</div>
                                <h3 class="text-2xl font-bold leading-none">{{ $selectedProduk->nama_produk }}</h3>
                                <p class="text-sm text-slate-400 mt-1">{{ $selectedProduk->kategori->nama ?? 'Umum' }}</p>
                            </div>
                        </div>

                        <!-- Statistik Stok -->
                        <div class="flex gap-8 text-center">
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Stok Saat Ini</p>
                                <div class="text-4xl font-extrabold mt-1 {{ $selectedProduk->stok <= 5 ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $selectedProduk->stok }}
                                    <span class="text-sm font-normal text-slate-500">{{ $selectedProduk->satuan }}</span>
                                </div>
                            </div>
                            <div class="hidden sm:block w-px bg-white/10"></div>
                            <div class="hidden sm:block">
                                <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Harga Jual</p>
                                <div class="text-xl font-bold mt-1 text-white">
                                    Rp {{ number_format($selectedProduk->harga_jual, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- JIKA BELUM PILIH PRODUK: STATISTIK UMUM -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Masuk (Bulan Ini)</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">+{{ number_format($totalMasuk) }}</h3>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-red-500 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Keluar (Bulan Ini)</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">-{{ number_format($totalKeluar) }}</h3>
                        </div>
                        <div class="p-3 bg-red-50 rounded-full text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Total Aktivitas</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($aktivitasBulanIni) }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            @endif

            <!-- FILTER & TABLE CARD -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                
                <!-- FILTER BAR (PERBAIKAN LAYOUT) -->
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <form method="GET" action="{{ route('stok_log.index') }}">
                        <div class="flex flex-col lg:flex-row gap-4 items-end">
                            
                            <!-- Filter Produk (Lebar) -->
                            <div class="w-full lg:flex-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cek Produk</label>
                                <select name="produk_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="this.form.submit()">
                                    <option value="">-- Pilih Produk untuk Cek Stok --</option>
                                    @foreach($produksForSearch as $p)
                                        <option value="{{ $p->id }}" {{ request('produk_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Tipe -->
                            <div class="w-full lg:w-40">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipe</label>
                                <select name="tipe" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua</option>
                                    <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Masuk (+)</option>
                                    <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Keluar (-)</option>
                                </select>
                            </div>

                            <!-- Filter Tanggal -->
                            <div class="w-full lg:w-auto">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal</label>
                                <div class="flex items-center gap-2">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full lg:w-36 border-gray-300 rounded-lg text-xs px-2 py-2">
                                    <span class="text-gray-400">-</span>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full lg:w-36 border-gray-300 rounded-lg text-xs px-2 py-2">
                                </div>
                            </div>

                            <!-- Button (Auto Width) -->
                            <div class="w-full lg:w-auto">
                                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg transition shadow-md whitespace-nowrap">
                                    Filter
                                </button>
                            </div>

                            <!-- Reset Button -->
                            @if(request()->anyFilled(['produk_id', 'tipe', 'start_date']))
                                <div class="w-full lg:w-auto">
                                    <a href="{{ route('stok_log.index') }}" class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg hover:bg-gray-50 transition">
                                        Reset
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- TABLE LIST -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($log->tanggal)->format('H:i') }}
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center 
                                                {{ $log->tipe == 'masuk' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                @if($log->tipe == 'masuk')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $log->produk->nama_produk ?? 'Deleted' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                            {{ $log->tipe == 'masuk' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $log->tipe == 'masuk' ? '+' : '-' }}{{ $log->jumlah }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-900 font-bold bg-gray-100 px-2 py-0.5 rounded w-fit mb-1">{{ $log->sumber }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs" title="{{ $log->keterangan }}">
                                            {{ $log->keterangan ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-xs font-medium text-gray-600">{{ $log->user->name ?? 'System' }}</span>
                                            <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        Belum ada riwayat aktivitas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>