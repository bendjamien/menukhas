<x-app-layout>
    <div>
        <div class="bg-sky-400 text-white p-6 rounded-lg shadow-md mb-8">
    @php
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';
        
        if (str_contains($companyName, ' ')) {
            $parts = explode(' ', $companyName);
            $firstWord = $parts[0];
            $restWords = implode(' ', array_slice($parts, 1));
        } else {
            $parts = preg_split('/(?=[A-Z])/', $companyName, -1, PREG_SPLIT_NO_EMPTY);
            
            if (count($parts) >= 2) {
                $firstWord = $parts[0]; 
                $restWords = implode('', array_slice($parts, 1));
            } else {
                $firstWord = $companyName;
                $restWords = '';
            }
        }
    @endphp

    <h1 class="text-2xl font-bold uppercase">
        SELAMAT DATANG DI DASHBOARD
        <span class="text-amber-500">{{ $firstWord }}</span><span class="text-emerald-600">{{ $restWords }}</span>
    </h1>
    <p class="mt-1 opacity-90">Kelola Transaksi Kasir Anda dengan Mudah</p>
</div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold">
                        Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
                <div class="p-3 bg-sky-100 rounded-lg">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold">
                        {{ $jumlahTransaksiHariIni }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
                <div class="p-3 bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.282-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.282.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Pelanggan</p>
                    <p class="text-2xl font-bold">
                        {{ $jumlahPelanggan }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4M4 7l8 4M4 7v10l8 4m0-14L4 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jenis Produk</p>
                    <p class="text-2xl font-bold">
                        {{ $jumlahProduk }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>