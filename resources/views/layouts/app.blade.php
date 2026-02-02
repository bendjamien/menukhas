<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MenuKhas') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            #chat-box::-webkit-scrollbar { width: 5px; }
            #chat-box::-webkit-scrollbar-track { background: #f1f5f9; }
            #chat-box::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            #chat-box::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

            /* Hide number input arrows */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type=number] {
                -moz-appearance: textfield;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-100">
        
        <div x-data="{ isSidebarOpen: false }" class="h-screen flex overflow-hidden">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-y-auto relative">
                <div x-show="isSidebarOpen" @click="isSidebarOpen = false" 
                     class="fixed inset-0 bg-black/50 z-30 md:hidden"
                     x-transition.opacity aria-hidden="true"></div>

                <!-- MODERN FLOATING NAVBAR -->
                <header class="sticky top-4 z-30 px-4 transition-all duration-300">
                    <div class="mx-auto max-w-7xl bg-white/80 backdrop-blur-xl border border-gray-100 shadow-xl shadow-gray-200/40 rounded-3xl px-4 py-2 flex justify-between items-center gap-4 h-16">
                        
                        <!-- 1. LEFT: Sidebar Toggle & Title -->
                        <div class="flex items-center gap-4 flex-shrink-0" 
                             x-data="{ 
                                date: '',
                                updateDate() {
                                    this.date = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                                }
                             }"
                             x-init="updateDate()">
                            <button @click.prevent="isSidebarOpen = !isSidebarOpen" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            
                            <div class="hidden md:flex flex-col">
                                <h2 class="text-base font-black text-gray-800 tracking-tight leading-none uppercase">
                                    {{ $header ?? 'MENUKHAS POS' }}
                                </h2>
                                <span class="text-[10px] font-bold text-gray-400 mt-1" x-text="date"></span>
                            </div>
                        </div>

                        <!-- 2. CENTER: Global Search Bar -->
                        <div class="hidden md:flex flex-1 max-w-sm relative" 
                             x-data="{
                                query: '',
                                results: [],
                                isLoading: false,
                                isOpen: false,
                                search() {
                                    if (this.query.length < 2) { this.results = []; this.isOpen = false; return; }
                                    this.isLoading = true;
                                    this.isOpen = true;
                                    
                                    fetch('{{ route('global.search') }}?q=' + this.query)
                                        .then(res => res.json())
                                        .then(data => {
                                            this.results = data;
                                            this.isLoading = false;
                                        });
                                }
                             }"
                             @click.outside="isOpen = false">
                            
                            <div class="relative w-full group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       x-model="query"
                                       @input.debounce.300ms="search()"
                                       @focus="if(query.length >= 2) isOpen = true"
                                       class="block w-full pl-9 pr-3 py-2 border-none rounded-2xl bg-gray-50/80 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:bg-white transition-all shadow-inner" 
                                       placeholder="Cari (Ctrl + K)..."
                                       @keydown.window.ctrl.k.prevent="$el.focus()">
                                
                                <!-- Loading Indicator -->
                                <div x-show="isLoading" class="absolute inset-y-0 right-3 flex items-center" style="display:none;">
                                    <svg class="animate-spin h-4 w-4 text-sky-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div x-show="isOpen && query.length >= 2" 
                                 x-transition.opacity
                                 class="absolute top-full mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 max-h-80 overflow-y-auto"
                                 style="display:none;">
                                
                                <template x-if="results.length === 0 && !isLoading">
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        Tidak ditemukan hasil untuk "<span x-text="query" class="font-bold"></span>"
                                    </div>
                                </template>

                                <ul class="divide-y divide-gray-50">
                                    <template x-for="item in results">
                                        <li>
                                            <a :href="item.url" class="flex items-center px-4 py-3 hover:bg-sky-50 transition gap-3 group">
                                                <div class="p-2 rounded-lg bg-gray-100 text-gray-500 group-hover:bg-white group-hover:text-sky-500 transition-colors" x-html="item.icon"></div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-800" x-text="item.text"></p>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-500" x-text="item.type"></span>
                                                        <span class="text-xs text-gray-500" x-text="item.subtext"></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <!-- 3. RIGHT: Widgets & Profile -->
                        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                            
                            <!-- Time Widget -->
                            <div class="hidden lg:flex items-center h-10 bg-sky-50/50 px-4 rounded-2xl border border-sky-100/50"
                                 x-data="{ 
                                    time: '', 
                                    updateTime() {
                                        const now = new Date();
                                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                                    }
                                 }"
                                 x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-sm font-black text-sky-600 font-mono leading-none" x-text="time"></span>
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                <button @click="open = !open" class="relative p-2.5 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-xl transition">
                                    @if(isset($globalNotifs) && count($globalNotifs) > 0)
                                        <span class="absolute top-2.5 right-3 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500 animate-bounce"></span>
                                    @endif
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </button>

                                <!-- Notif Dropdown -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right overflow-hidden"
                                     style="display:none;">
                                    
                                    <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                                        <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Notifikasi</span>
                                        <span class="text-[10px] text-gray-400">{{ isset($globalNotifs) ? count($globalNotifs) : 0 }} Baru</span>
                                    </div>

                                    <div class="max-h-64 overflow-y-auto">
                                        @if(isset($globalNotifs))
                                            @forelse($globalNotifs as $notif)
                                                <a href="{{ $notif['link'] }}" class="block px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                                    <div class="flex gap-3">
                                                        <div class="flex-shrink-0 mt-1">
                                                            @if($notif['type'] == 'warning')
                                                                <div class="bg-orange-100 text-orange-500 p-1.5 rounded-full">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                                </div>
                                                            @else
                                                                <div class="bg-blue-100 text-blue-500 p-1.5 rounded-full">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-bold text-gray-800">{{ $notif['title'] }}</p>
                                                            <p class="text-xs text-gray-500 mt-0.5">{{ $notif['message'] }}</p>
                                                            <p class="text-[10px] text-gray-400 mt-1">{{ $notif['time'] }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            @empty
                                                <div class="px-4 py-8 text-center">
                                                    <p class="text-gray-400 text-sm">Tidak ada notifikasi.</p>
                                                </div>
                                            @endforelse
                                        @else
                                            <div class="px-4 py-8 text-center">
                                                <p class="text-gray-400 text-sm">Tidak ada notifikasi.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Dropdown -->
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                                    <div class="text-right hidden sm:block">
                                        <p class="text-sm font-bold text-gray-800 group-hover:text-sky-600 transition">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500 font-medium capitalize">{{ Auth::user()->role }}</p>
                                    </div>
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-sky-400 to-blue-600 p-0.5 shadow-md group-hover:shadow-sky-200 transition">
                                            <div class="w-full h-full rounded-[10px] bg-white flex items-center justify-center overflow-hidden">
                                                @if(Auth::user()->avatar)
                                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                                                @else
                                                    <span class="font-bold text-sky-600 text-xs">{{ substr(Auth::user()->name, 0, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                     class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right">
                                    
                                    <div class="px-4 py-3 border-b border-gray-50 mb-2">
                                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Signed in as</p>
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->email }}</p>
                                    </div>

                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-sky-50 hover:text-sky-700 transition">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Profil Saya
                                    </a>
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('pengaturan.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-sky-50 hover:text-sky-700 transition">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Pengaturan
                                        </a>
                                    @endif

                                    <div class="border-t border-gray-100 my-2"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6 pb-24">
                    {{ $slot }}
                </main>
            </div>
        </div> 

        <div x-data="chatBot()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-4 font-sans">

            <div x-show="isOpen" 
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10 scale-90"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-10 scale-90"
                 class="w-[350px] md:w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col relative">
                
                <div x-show="confirmDelete" 
                     x-transition.opacity
                     class="absolute inset-0 z-50 bg-white/90 backdrop-blur-sm flex items-center justify-center p-6">
                    <div class="text-center w-full">
                        <div class="w-12 h-12 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </div>
                        <h3 class="text-gray-800 font-bold text-lg">Hapus Percakapan?</h3>
                        <p class="text-gray-500 text-sm mb-5">Riwayat chat akan hilang permanen.</p>
                        <div class="flex gap-2 justify-center">
                            <button @click="confirmDelete = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Batal</button>
                            <button @click="executeClearChat" class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition shadow-md">Ya, Hapus</button>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-sky-500 to-blue-600 p-4 flex items-center justify-between shadow-md">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-sky-600 font-bold text-lg shadow-inner">
                            M
                        </div>
                        <div class="text-white">
                            <h3 class="font-bold text-sm tracking-wide">MenuKhas Assistant</h3>
                            <div class="flex items-center gap-1.5">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                                </span>
                                <p class="text-[11px] font-medium opacity-90">Online</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-1">
                        <button @click="confirmDelete = true" title="Hapus Riwayat Chat" class="text-white/80 hover:text-red-200 hover:bg-white/10 p-1.5 rounded-full transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <button @click="isOpen = false" class="text-white/80 hover:text-white hover:bg-white/20 p-1.5 rounded-full transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-6" id="chat-box">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-blue-200 rounded-full flex items-center justify-center text-sky-700 text-xs font-bold flex-shrink-0 shadow-sm">M</div>
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] text-gray-500 font-semibold ml-1">Khas Assistant</span>
                            <div class="bg-white p-3.5 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 text-sm text-gray-700 leading-relaxed max-w-[260px]">
                                Halo, <b>{{ Auth::user()->name }}</b>! 👋 <br>Saya Khas Assistant. Ada yang bisa dibantu?
                            </div>
                        </div>
                    </div>

                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex items-end gap-2" :class="msg.sender === 'user' ? 'flex-row-reverse' : ''">
                            
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0"
                                 :class="msg.sender === 'user' ? 'bg-sky-500 text-white' : 'bg-white text-sky-600 border border-sky-100'">
                                <span x-text="msg.initial"></span>
                            </div>

                            <div class="flex flex-col gap-1 max-w-[80%]">
                                <span class="text-[10px] text-gray-400 font-bold" 
                                      :class="msg.sender === 'user' ? 'text-right mr-1' : 'ml-1'"
                                      x-text="msg.sender === 'user' ? userName : 'Mks Bot'"></span>
                                
                                <div class="p-3.5 text-sm shadow-sm leading-relaxed break-words"
                                     :class="msg.sender === 'user' 
                                        ? 'bg-sky-500 text-white rounded-2xl rounded-tr-none' 
                                        : 'bg-white text-gray-800 rounded-2xl rounded-tl-none border border-gray-100'">
                                    <span x-text="msg.text"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="isLoading" class="flex items-start gap-2.5">
                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center text-sky-700 text-xs font-bold">M</div>
                        <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm border border-gray-100">
                            <div class="flex space-x-1.5">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white border-t border-gray-100">
                    <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                        <input type="text" x-model="userInput" placeholder="Tulis pesan..." 
                               class="flex-1 border border-gray-200 bg-gray-50 rounded-full px-5 py-3 text-sm focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition placeholder-gray-400">
                        <button type="submit" :disabled="isLoading || userInput.trim() === ''"
                                class="bg-sky-500 text-white p-3 rounded-full hover:bg-sky-600 shadow-lg shadow-sky-200 disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed transition-all transform active:scale-95 flex items-center justify-center">
                            <svg class="w-5 h-5 transform rotate-90 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                    <div class="text-center mt-2">
                         <span class="text-[10px] text-gray-300 font-medium tracking-wide">Powered by Gemini AI</span>
                    </div>
                </div>
            </div>

            <button @click="isOpen = !isOpen" 
                    class="group w-16 h-16 bg-sky-500 hover:bg-sky-600 text-white rounded-full shadow-2xl shadow-sky-400/50 flex items-center justify-center transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-sky-300">
                <svg x-show="!isOpen" class="w-8 h-8 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <svg x-show="isOpen" class="w-8 h-8 rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

        </div>

        <script>
            function chatBot() {
                return {
                    isOpen: false,
                    confirmDelete: false,
                    userInput: '',
                    isLoading: false,
                    userName: '{{ Auth::user()->name }}', 
                    userInitial: '{{ substr(Auth::user()->name, 0, 1) }}',
                    messages: JSON.parse(localStorage.getItem('mks_chat_history')) || [],

                    init() {
                        if (this.messages.length > 0) this.scrollToBottom();
                        this.$watch('messages', (val) => localStorage.setItem('mks_chat_history', JSON.stringify(val)));
                    },

                    sendMessage() {
                        if (this.userInput.trim() === '') return;
                        const msg = this.userInput;
                        this.messages.push({ text: msg, sender: 'user', initial: this.userInitial });
                        this.userInput = '';
                        this.isLoading = true;
                        this.scrollToBottom();

                        fetch('{{ route("chat.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message: msg })
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.messages.push({ text: data.reply, sender: 'bot', initial: 'M' });
                            this.isLoading = false;
                            this.scrollToBottom();
                        })
                        .catch(err => {
                            this.messages.push({ text: "Koneksi Error.", sender: 'bot', initial: '!' });
                            this.isLoading = false;
                            this.scrollToBottom();
                        });
                    },

                    executeClearChat() {
                        this.messages = [];
                        localStorage.removeItem('mks_chat_history');
                        this.confirmDelete = false; 
                    },

                    scrollToBottom() {
                        setTimeout(() => {
                            const el = document.getElementById('chat-box');
                            el.scrollTop = el.scrollHeight;
                        }, 100);
                    }
                }
            }
        </script>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
            @if (session('toast_success'))
                <div x-data x-init="Toastify({ text: `{!! session('toast_success') !!}`, duration: 3000, gravity: 'top', position: 'right', escapeMarkup: false, style: { background: 'linear-gradient(to right, #38bdf8, #3b82f6)', borderRadius: '0.5rem' } }).showToast();"></div>
            @endif
        
            @if (session('toast_danger'))
                <div x-data x-init="Toastify({ text: `{!! session('toast_danger') !!}`, duration: 3000, gravity: 'top', position: 'right', escapeMarkup: false, style: { background: 'linear-gradient(to right, #ef4444, #dc2626)', borderRadius: '0.5rem' } }).showToast();"></div>
            @endif
        
            @if ($errors->any())
                <div x-data x-init="Toastify({ text: `{{ $errors->first() }}`, duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #ef4444, #dc2626)', borderRadius: '0.5rem' } }).showToast();"></div>
            @endif
        
    </body>
</html>