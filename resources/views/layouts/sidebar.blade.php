<aside 
    class="w-64 flex-shrink-0 bg-white p-4 flex flex-col fixed inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out -translate-x-full md:relative md:translate-x-0"
    :class="{ 'translate-x-0': isSidebarOpen }"
>
    <div class="h-16 flex items-center justify-center text-2xl font-bold text-slate-800">
        MenuKhas
    </div>

    <nav class="flex-grow mt-4 overflow-y-auto">
        <ul class="flex flex-col space-y-2">
            
            @if(Auth::user()->role !== 'owner')
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="relative flex items-center h-12 px-4 rounded-lg
                          {{ request()->is('dashboard') ? 'bg-sky-500 text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Dashboard</span>
                    <span class="absolute right-0 h-full w-2 rounded-r-lg {{ request()->is('dashboard') ? 'bg-sky-600' : '' }}"></span>
                </a>
            </li>
            @endif

            @if(Auth::user()->role !== 'owner')
            <li>
                <a href="{{ route('pos.index') }}" 
                   class="relative flex items-center h-12 px-4 rounded-lg
                          {{ request()->is('pos*') ? 'bg-sky-500 text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    <span class="inline-flex justify-center items-center">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H7a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Input Transaksi</span>
                    <span class="absolute right-0 h-full w-2 rounded-r-lg {{ request()->is('pos*') ? 'bg-sky-600' : '' }}"></span>
                </a>
            </li>
            @endif
            
            @if(Auth::user()->role !== 'owner')
            <li x-data="{ open: {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'true' : 'false' }} }" class="space-y-2">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-lg
                               {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'bg-sky-500 text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4M4 7v4c0 2.21 3.582 4 8 4s8-1.79 8-4V7"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Master Data</span>
                    <span class="absolute right-0 h-full w-2 rounded-r-lg {{ (request()->is('pelanggan*') || request()->is('kategori*') || request()->is('produk*')) ? 'bg-sky-600' : '' }}"></span>
                    <svg class="w-4 h-4 absolute right-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open" class="ml-6 space-y-2">
                    <li><a href="{{ route('pelanggan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pelanggan*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">Data Pelanggan</a></li>
                    @if(Auth::user()->role == 'admin')
                        <li><a href="{{ route('kategori.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('kategori*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">Data Kategori</a></li>
                        <li><a href="{{ route('produk.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('produk*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">Data Produk</a></li>
                    @endif
                </ul>
            </li>
            @endif

            <li x-data="{ open: {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || request()->is('pelanggan*')) ? 'true' : 'false' }} }" class="space-y-2">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-lg
                               {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || (Auth::user()->role == 'owner' && request()->is('pelanggan*'))) ? 'bg-sky-500 text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Laporan</span>
                    <span class="absolute right-0 h-full w-2 rounded-r-lg {{ (request()->is('transaksi*') || request()->is('stok-log*') || request()->is('pembayaran*') || request()->is('laporan*') || (Auth::user()->role == 'owner' && request()->is('pelanggan*'))) ? 'bg-sky-600' : '' }}"></span>
                    <svg class="w-4 h-4 absolute right-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </button>

                <ul x-show="open" class="ml-6 space-y-2">
                    <li>
                        <a href="{{ route('transaksi.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('transaksi*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                            Laporan Transaksi
                        </a>
                    </li>

                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'owner')
                        <li>
                            <a href="{{ route('laporan.pendapatan') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('laporan/pendapatan*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Laporan Pendapatan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('stok_log.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('stok-log*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Laporan Stok
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pembayaran.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pembayaran*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Laporan Pembayaran
                            </a>
                        </li>
                        @if(Auth::user()->role == 'owner')
                        <li>
                            <a href="{{ route('pelanggan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pelanggan*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                                Laporan Data Pelanggan
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>
            </li>

            @if(Auth::user()->role == 'admin')
            <li x-data="{ open: {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'true' : 'false' }} }" class="space-y-2">
                <button @click="open = !open"
                        class="relative flex items-center w-full h-12 px-4 rounded-lg
                               {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'bg-sky-500 text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Administrasi</span>
                    <span class="absolute right-0 h-full w-2 rounded-r-lg {{ (request()->is('users*') || request()->is('pengaturan*')) ? 'bg-sky-600' : '' }}"></span>
                    <svg class="w-4 h-4 absolute right-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open" class="ml-6 space-y-2">
                    <li><a href="{{ route('users.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('users*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">Manajemen Akun</a></li>
                    <li><a href="{{ route('pengaturan.index') }}" class="block px-4 py-2 rounded-lg text-sm {{ request()->is('pengaturan*') ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">Pengaturan</a></li>
                </ul>
            </li>
            @endif

        </ul>
    </nav>
    
    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full h-12 px-4 rounded-lg bg-gray-100 text-slate-700 hover:bg-gray-200">
                 <span class="inline-flex justify-center items-center">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </span>
                <span class="ml-3 text-md font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>