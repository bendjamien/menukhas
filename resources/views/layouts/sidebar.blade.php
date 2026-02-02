<aside 
    class="fixed inset-y-0 left-0 z-40 w-64 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:inset-auto md:h-[calc(100vh-2rem)] md:m-4 md:rounded-3xl bg-white shadow-2xl shadow-gray-200/50 border border-gray-100 flex flex-col"
    :class="{ 'translate-x-0': isSidebarOpen, '-translate-x-full': !isSidebarOpen }"
>
    <!-- 1. BRAND LOGO SECTION -->
    <div class="h-20 flex items-center justify-center px-6 border-b border-gray-50 bg-gradient-to-b from-white to-gray-50/50 md:rounded-t-3xl">
        @php
            $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';
            // Logic pemisahan nama (sama seperti sebelumnya)
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

            // CEK STATUS KUNCI
            $isLocked = false;
            if (auth()->check()) {
                $todayDate = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');
                $isLocked = \App\Models\Absensi::where('user_id', auth()->id())
                    ->where('tanggal', $todayDate)
                    ->whereNotNull('waktu_keluar')
                    ->exists();
            }
        @endphp

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
            <div class="w-8 h-8 bg-sky-600 text-white rounded-lg flex items-center justify-center font-bold text-lg shadow-lg shadow-sky-200 group-hover:rotate-12 transition-transform">
                {{ substr($firstWord, 0, 1) }}
            </div>
            <div class="flex flex-col leading-none">
                <span class="font-extrabold text-xl text-gray-800 tracking-tight group-hover:text-sky-600 transition-colors">
                    {{ $firstWord }}<span class="text-sky-600">{{ $restWords }}</span>
                </span>
                <span class="text-[9px] text-gray-400 font-medium tracking-widest uppercase">Point of Sales</span>
            </div>
        </a>
    </div>

    <!-- 2. NAVIGATION LINKS -->
    <nav class="flex-grow overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
        
        <!-- DASHBOARD -->
        <a href="{{ route('dashboard') }}" 
           class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-200
                  {{ request()->is('dashboard') 
                     ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-100' 
                     : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            
            <svg class="flex-shrink-0 w-5 h-5 transition-colors duration-200 {{ request()->is('dashboard') ? 'text-sky-600' : 'text-gray-400 group-hover:text-gray-600' }}" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="ml-3">Dashboard</span>
            
            @if(request()->is('dashboard'))
                <span class="absolute right-3 w-1.5 h-1.5 rounded-full bg-sky-500 shadow-md shadow-sky-300"></span>
            @endif
        </a>

        <!-- INPUT TRANSAKSI (POS) -->
        @if(Auth::user()->role !== 'owner')
        <a href="{{ route('pos.index') }}" 
           class="group relative flex items-center px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-200 {{ $isLocked ? 'opacity-50 pointer-events-none grayscale' : '' }}
                  {{ request()->is('pos*') 
                     ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-100' 
                     : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            
            <svg class="flex-shrink-0 w-5 h-5 transition-colors duration-200 {{ request()->is('pos*') ? 'text-sky-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="ml-3">Kasir / POS</span>

            @if($isLocked) <svg class="w-4 h-4 ml-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> @endif
            @if(request()->is('pos*'))
                <span class="absolute right-3 w-1.5 h-1.5 rounded-full bg-sky-500 shadow-md shadow-sky-300"></span>
            @endif
        </a>
        @endif
        
        <!-- SEPARATOR -->
        <div class="py-2">
            <div class="border-t border-gray-100"></div>
            <p class="px-4 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Master Data</p>
        </div>

        <!-- MASTER DATA (Dropdown) -->
        @if(Auth::user()->role !== 'owner')
        <div x-data="{ open: {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'true' : 'false' }} }" class="{{ $isLocked ? 'opacity-50 pointer-events-none grayscale' : '' }}">
            <button @click="open = !open"
                    class="group relative flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-200
                           {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) 
                              ? 'bg-sky-50 text-sky-700' 
                              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-5 h-5 transition-colors duration-200 {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'text-sky-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="ml-3">Manajemen Data</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            
            <div x-show="open" x-transition.origin.top class="mt-1 space-y-1 pl-11 pr-2">
                <a href="{{ route('pelanggan.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('pelanggan*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    Pelanggan
                </a>
                @if(Auth::user()->role == 'admin')
                    <a href="{{ route('kategori.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('kategori*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Kategori Menu
                    </a>
                    <a href="{{ route('produk.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('produk*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Produk / Menu
                    </a>
                @endif
            </div>
        </div>
        @endif

        <!-- LAPORAN (Dropdown) -->
        <div x-data="{ open: {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || (Auth::user()->role == 'owner' && request()->is('pelanggan*'))) ? 'true' : 'false' }} }" class="{{ $isLocked ? 'opacity-50 pointer-events-none grayscale' : '' }}">
            <button @click="open = !open"
                    class="group relative flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-200
                           {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*')) 
                              ? 'bg-sky-50 text-sky-700' 
                              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-5 h-5 transition-colors duration-200 {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*')) ? 'text-sky-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="ml-3">Laporan & Keuangan</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open" x-transition.origin.top class="mt-1 space-y-1 pl-11 pr-2">
                <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('transaksi*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    Riwayat Transaksi
                </a>
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'owner')
                    <a href="{{ route('laporan.pendapatan') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('laporan/pendapatan*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Laporan Pendapatan
                    </a>
                    <a href="{{ route('laporan.absensi') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('laporan/absensi*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Laporan Absensi
                    </a>
                    <a href="{{ route('stok_log.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('stok-log*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        Log Stok Barang
                    </a>
                @endif
            </div>
        </div>

        @if(Auth::user()->role == 'admin')
        <!-- ADMINISTRASI (Dropdown) -->
        <div x-data="{ open: {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'true' : 'false' }} }" class="{{ $isLocked ? 'opacity-50 pointer-events-none grayscale' : '' }}">
            <button @click="open = !open"
                    class="group relative flex items-center justify-between w-full px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-200
                           {{ (request()->is('users*') || request()->is('pengaturan*')) 
                              ? 'bg-sky-50 text-sky-700' 
                              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-5 h-5 transition-colors duration-200 {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'text-sky-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="ml-3">Admin System</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open" x-transition.origin.top class="mt-1 space-y-1 pl-11 pr-2">
                <a href="{{ route('users.index') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('users*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    <span>Manajemen User</span>
                    @php $pendingPinCount = \App\Models\User::where('request_new_pin', true)->count(); @endphp
                    @if($pendingPinCount > 0)
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm">{{ $pendingPinCount }}</span>
                    @endif
                </a>
                <a href="{{ route('pengaturan.index') }}" class="block px-3 py-2 rounded-xl text-sm transition-colors {{ request()->is('pengaturan*') ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    Pengaturan
                </a>
            </div>
        </div>
        @endif

    </nav>

    <!-- 3. FOOTER PROFILE & LOGOUT -->
    <div class="p-4 mt-auto border-t border-gray-50 md:rounded-b-3xl bg-gray-50/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-red-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200 group">
                <div class="p-2 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-sm font-bold">Logout</span>
                    <span class="text-[10px] text-red-400 opacity-70">Keluar sesi</span>
                </div>
            </button>
        </form>
    </div>
</aside>

<!-- STYLE UNTUK HIDE SCROLLBAR TAPI TETAP BISA SCROLL -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: transparent;
        border-radius: 20px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: #e2e8f0;
    }
</style>