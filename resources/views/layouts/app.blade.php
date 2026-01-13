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

                <header class="sticky top-6 z-20 mx-6 mt-6 bg-white/90 backdrop-blur-md shadow-sm rounded-2xl border border-gray-100">
                    <div class="px-6 py-4 flex justify-between items-center">
                        <button @click.prevent="isSidebarOpen = !isSidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>

                        <div class="hidden md:flex items-center gap-6 flex-1 justify-start" 
                             x-data="{ 
                                date: '', 
                                time: '', 
                                greeting: '',
                                updateTime() {
                                    const now = new Date();
                                    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                                    this.date = now.toLocaleDateString('id-ID', options);
                                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':') + ' WIB';

                                    const hour = now.getHours();
                                    if (hour < 11) this.greeting = 'Selamat Pagi,';
                                    else if (hour < 15) this.greeting = 'Selamat Siang,';
                                    else if (hour < 18) this.greeting = 'Selamat Sore,';
                                    else this.greeting = 'Selamat Malam,';
                                }
                             }"
                             x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                            
                            <div class="text-left hidden lg:block">
                                <p class="text-xs text-gray-500 font-medium" x-text="greeting"></p>
                                <p class="text-sm font-bold text-gray-800 leading-tight">{{ Auth::user()->role }}</p>
                            </div>

                            <div class="h-8 w-px bg-gray-200 hidden lg:block"></div>

                            <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-full border border-gray-200/60 shadow-sm">
                                <div class="bg-indigo-100 p-1.5 rounded-full text-sky-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider leading-none" x-text="date"></p>
                                    <p class="text-xl font-bold text-sky-600 leading-none font-mono mt-0.5" x-text="time"></p>
                                </div>
                            </div>
                        </div>

                        <div x-data="{ open: false }" @click.away="open = false" class="relative ml-4">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-10 h-10 bg-sky-500 hover:bg-sky-600 rounded-full flex items-center justify-center text-white font-bold shadow-md shadow-sky-200 transform hover:scale-105 transition duration-200">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden md:block text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 z-50 border border-gray-100 ring-1 ring-black ring-opacity-5">
                                <div class="px-4 py-3 border-b border-gray-50">
                                    <p class="text-sm font-semibold text-gray-900">Akun Saya</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</button>
                                </form>
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
            <div x-data x-init="Toastify({ text: '{{ session('toast_success') }}', duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #38bdf8, #3b82f6)', borderRadius: '0.5rem' } }).showToast();"></div>
        @endif
        @if (session('toast_danger'))
            <div x-data x-init="Toastify({ text: '{{ session('toast_danger') }}', duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #ef4444, #dc2626)', borderRadius: '0.5rem' } }).showToast();"></div>
        @endif
        @if ($errors->any())
            <div x-data x-init="Toastify({ text: '{{ $errors->first() }}', duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #ef4444, #dc2626)', borderRadius: '0.5rem' } }).showToast();"></div>
        @endif
        
    </body>
</html>