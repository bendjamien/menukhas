<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ isSidebarOpen: false }" class="h-screen flex bg-slate-100 overflow-hidden">
            
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-y-auto">
                <div 
                    x-show="isSidebarOpen" 
                    @click="isSidebarOpen = false" 
                    x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/50 z-30 md:hidden"
                    aria-hidden="true"
                ></div>

                <header class="sticky top-6 z-20 mx-8 mt-6 bg-white/95 backdrop-blur-md shadow-lg rounded-xl border border-gray-100">
                    <div class="p-4 flex justify-between items-center">
                        <button 
                            @click.prevent="isSidebarOpen = !isSidebarOpen" 
                            class="md:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >
                            <span class="sr-only">Buka menu</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="relative w-full max-w-xs hidden md:block">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                        </div>

                        <div x-data="{ open: false }" @click.away="open = false" class="relative ml-4">
                            <button @click="open = !open" class="w-10 h-10 bg-gradient-to-r from-sky-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold shadow-md hover:shadow-lg transition-shadow duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                                
                                <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
                                    <p class="font-semibold">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profil Saya
                                    </div>
                                </a>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Logout
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="p-8 pb-24">
                    {{ $slot }}
                </main>
            </div>

        </div> 
        
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        
        @if (session('toast_success'))
            <div 
                x-data 
                x-init="
                    Toastify({
                        text: '{{ session('toast_success') }}',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                            background: 'linear-gradient(to right, #38bdf8, #3b82f6)',
                            'border-radius': '0.5rem',
                            'box-shadow': '0 10px 25px rgba(0, 0, 0, 0.1)'
                        }
                    }).showToast();
                ">
            </div>
        @endif

        @if (session('toast_danger'))
            <div 
                x-data 
                x-init="
                    Toastify({
                        text: '{{ session('toast_danger') }}',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                            background: 'linear-gradient(to right, #ef4444, #dc2626)',
                            'border-radius': '0.5rem',
                            'box-shadow': '0 10px 25px rgba(0, 0, 0, 0.1)'
                        }
                    }).showToast();
                ">
            </div>
        @endif

        @if ($errors->any())
            <div 
                x-data 
                x-init="
                    Toastify({
                        text: '{{ $errors->first() }}',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                            background: 'linear-gradient(to right, #ef4444, #dc2626)',
                            'border-radius': '0.5rem',
                            'box-shadow': '0 10px 25px rgba(0, 0, 0, 0.1)'
                        }
                    }).showToast();
                ">
            </div>
        @endif
        
    </body>
</html>