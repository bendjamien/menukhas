<!-- 
    PERBAIKAN MOBILE:
    - 'fixed ... z-40' : Membuat sidebar menjadi overlay.
    - 'transition-transform ... -translate-x-full' : Menyembunyikan sidebar ke kiri.
    - 'md:relative md:translate-x-0' : Mengembalikan ke normal di desktop.
    - ':class="{ 'translate-x-0': isSidebarOpen }"' : Perintah Alpine.js untuk memunculkan sidebar.
-->
<aside 
    class="w-64 flex-shrink-0 bg-white p-4 flex flex-col fixed inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out -translate-x-full md:relative md:translate-x-0"
    :class="{ 'translate-x-0': isSidebarOpen }"
>
    <div class="h-16 flex items-center justify-center text-2xl font-bold text-slate-800">
        MenuKhas
    </div>

    <nav class="flex-grow mt-4">
        <ul class="flex flex-col space-y-2">
            
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="relative flex items-center h-12 px-4 rounded-lg
                          {{ request()->is('dashboard') 
                             ? 'bg-sky-500 text-white' 
                             : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    
                    <span class="inline-flex justify-center items-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Dashboard</span>

                    <span class="absolute right-0 h-full w-2 rounded-r-lg
                                {{ request()->is('dashboard') ? 'bg-sky-600' : '' }}">
                    </span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="relative flex items-center h-12 px-4 rounded-lg
                          {{ request()->is('pelanggan*') 
                             ? 'bg-sky-500 text-white' 
                             : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    
                    <span class="inline-flex justify-center items-center">
                         <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </span>
                    <span class="ml-3 text-md font-medium">Data Pelanggan</span>

                    <span class="absolute right-0 h-full w-2 rounded-r-lg
                                {{ request()->is('pelanggan*') ? 'bg-sky-600' : '' }}">
                    </span>
                </a>
            </li>

        </ul>
    </nav>

    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full h-12 px-4 rounded-lg bg-gray-100 text-slate-700 hover:bg-gray-200">
                 <span class="inline-flex justify-center items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </span>
                <span class="ml-3 text-md font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>

