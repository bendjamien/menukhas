<aside 
    class="w-64 flex-shrink-0 bg-white p-4 flex flex-col fixed inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out -translate-x-full md:relative md:translate-x-0"
    :class="{ 'translate-x-0': isSidebarOpen }"
>
    <div class="h-16 flex items-center justify-center border-b border-gray-100 mb-4 px-4">
        @php
            $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';
            
            if (str_contains($companyName, ' ')) {
                $parts = explode(' ', $companyName);
                $firstWord = $parts[0];
                $restWords = implode(' ', array_slice($parts, 1));
            } else {
                $parts = preg_split('/(?=[A-Z])/', $companyName, -1, PREG_SPLIT_NO_EMPTY);
                
                if (count($parts) >= 2) {
                    $firstWord = $parts[0]; // "Menu"
                    $restWords = implode('', array_slice($parts, 1)); // "Khas"
                } else {
                    $firstWord = $companyName;
                    $restWords = '';
                }
            }
        @endphp

        <a href="{{ route('dashboard') }}" class="flex items-center gap-1 font-bold text-2xl tracking-wide hover:scale-105 transition-transform duration-300">
            <span class="text-yellow-500">{{ $firstWord }}</span>
            
            <span class="text-emerald-600">{{ $restWords }}</span>
        </a>
    </div>

    <nav class="flex-grow overflow-y-auto custom-scrollbar">
        <ul class="flex flex-col space-y-2">
            
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="relative flex items-center h-12 px-4 rounded-xl transition-all duration-200
                          {{ request()->is('dashboard') ? 'bg-sky-50 text-sky-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </span>
                    <span class="ml-3 text-sm font-medium">Dashboard</span>
                    @if(request()->is('dashboard'))
                        <span class="absolute right-0 top-1/2 transform -translate-y-1/2 w-1.5 h-8 bg-sky-600 rounded-l-full"></span>
                    @endif
                </a>
            </li>

            @if(Auth::user()->role !== 'owner')
            <li>
                <a href="{{ route('pos.index') }}" 
                   class="relative flex items-center h-12 px-4 rounded-xl transition-all duration-200
                          {{ request()->is('pos*') ? 'bg-sky-50 text-sky-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="inline-flex justify-center items-center">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </span>
                    <span class="ml-3 text-sm font-medium">Input Transaksi</span>
                    @if(request()->is('pos*'))
                        <span class="absolute right-0 top-1/2 transform -translate-y-1/2 w-1.5 h-8 bg-sky-600 rounded-l-full"></span>
                    @endif
                </a>
            </li>
            @endif
            
            @if(Auth::user()->role !== 'owner')
            <li x-data="{ open: {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-xl transition-all duration-200 justify-between
                               {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'bg-sky-50 text-sky-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <span class="inline-flex justify-center items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </span>
                        <span class="ml-3 text-sm font-medium">Master Data</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <ul x-show="open" x-transition.origin.top class="mt-1 space-y-1 px-2">
                    <li>
                        <a href="{{ route('pelanggan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pelanggan*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            Data Pelanggan
                        </a>
                    </li>
                    @if(Auth::user()->role == 'admin')
                        <li>
                            <a href="{{ route('kategori.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('kategori*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Data Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('produk.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('produk*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Data Produk
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @endif

            <li x-data="{ open: {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || (Auth::user()->role == 'owner' && request()->is('pelanggan*'))) ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-xl transition-all duration-200 justify-between
                               {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || (Auth::user()->role == 'owner' && request()->is('pelanggan*'))) ? 'bg-sky-50 text-sky-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <span class="inline-flex justify-center items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </span>
                        <span class="ml-3 text-sm font-medium">Laporan</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <ul x-show="open" x-transition.origin.top class="mt-1 space-y-1 px-2">
                    <li>
                        <a href="{{ route('transaksi.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('transaksi*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            Riwayat Transaksi
                        </a>
                    </li>

                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'owner')
                        
                        <li>
                            <a href="{{ route('laporan.absensi') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('laporan/absensi*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Laporan Absensi
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('laporan.pendapatan') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('laporan/pendapatan*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Laporan Pendapatan
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('stok_log.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('stok-log*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Laporan Stok Barang
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('pembayaran.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pembayaran*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Laporan Pembayaran
                            </a>
                        </li>

                        @if(Auth::user()->role == 'owner')
                        <li>
                            <a href="{{ route('pelanggan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pelanggan*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                                Data Pelanggan
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>
            </li>

            @if(Auth::user()->role == 'admin')
            <li x-data="{ open: {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-xl transition-all duration-200 justify-between
                               {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'bg-sky-50 text-sky-600 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <span class="inline-flex justify-center items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span class="ml-3 text-sm font-medium">Administrasi</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <ul x-show="open" x-transition.origin.top class="mt-1 space-y-1 px-2">
                    <li>
                        <a href="{{ route('users.index') }}" class="relative flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->is('users*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            <span>Manajemen Akun</span>
                            @php
                                $pendingPinCount = \App\Models\User::where('request_new_pin', true)->count();
                            @endphp
                            @if($pendingPinCount > 0)
                                <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $pendingPinCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pengaturan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pengaturan*') ? 'bg-sky-100 text-sky-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            Pengaturan
                        </a>
                    </li>
                </ul>
            </li>
            @endif

        </ul>
    </nav>
    
    <div class="mt-auto border-t border-gray-100 pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full h-12 px-4 rounded-xl text-red-500 hover:bg-red-50 transition-all duration-200">
                 <span class="inline-flex justify-center items-center">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </span>
                <span class="ml-3 text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>