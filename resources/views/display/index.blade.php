<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Display - {{ config('app.name', 'MenuKhas') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #ffffff; color: #1f2937; }
        .order-card { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .custom-scrollbar::-webkit-scrollbar { width: 0px; }
    </style>
</head>
<body class="h-full overflow-hidden select-none antialiased">
    
    <div class="h-full flex flex-col" 
         x-data="{ 
            preparing: [],
            ready: [],
            lastReadyCount: 0,
            now: new Date(),
            
            async fetchData() {
                try {
                    let res = await fetch('{{ route('display.api.queue') }}?t=' + new Date().getTime());
                    let data = await res.json();
                    if (data.ready.length > this.lastReadyCount) { this.playAlert(); }
                    this.preparing = data.preparing;
                    this.ready = data.ready;
                    this.lastReadyCount = data.ready.length;
                } catch (e) { console.error(e); }
            },
            
            playAlert() {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                audio.play();
            },

            get currentTime() {
                return this.now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            },

            init() {
                this.fetchData();
                setInterval(() => this.fetchData(), 3000);
                setInterval(() => { this.now = new Date() }, 1000);
            }
         }">
        
        <!-- HEADER -->
        <header class="h-24 bg-white border-b border-gray-100 flex items-center justify-between px-12 shrink-0">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-sky-100">
                    {{ substr(config('app.name'), 0, 1) }}
                </div>
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 uppercase tracking-widest leading-none">{{ config('app.name', 'MenuKhas') }}</h1>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-[0.3em] mt-1.5">Order Status Terminal</p>
                </div>
            </div>
            
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-4xl font-semibold text-gray-900 tabular-nums tracking-tighter" x-text="currentTime"></p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ now()->format('D, d M Y') }}</p>
                </div>
            </div>
        </header>

        <!-- MAIN AREA -->
        <main class="flex-grow flex overflow-hidden">
            
            <!-- PREPARING (LEFT) -->
            <section class="flex-grow flex flex-col border-r border-gray-50 bg-gray-50/30">
                <div class="h-20 flex items-center px-12 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-[0.3em] flex items-center gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        Sedang Disiapkan
                    </h2>
                </div>
                <div class="flex-grow p-12 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-8">
                        <template x-for="order in preparing" :key="order.id">
                            <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                                <span class="text-3xl font-bold text-gray-800 tabular-nums" x-text="'#' + order.id"></span>
                                <span class="text-[9px] font-semibold text-gray-400 uppercase mt-2 tracking-widest truncate w-full text-center" x-text="order.nama"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- READY (RIGHT) -->
            <section class="w-[450px] flex flex-col bg-white">
                <div class="h-20 flex items-center px-12 border-b border-gray-100">
                    <h2 class="text-xs font-bold text-emerald-500 uppercase tracking-[0.3em] flex items-center gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Siap Diambil
                    </h2>
                </div>
                <div class="flex-grow p-12 overflow-y-auto custom-scrollbar">
                    <div class="space-y-6">
                        <template x-for="order in ready" :key="order.id">
                            <div class="p-8 bg-emerald-50 rounded-[2rem] border border-emerald-100 flex items-center justify-between shadow-sm">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-[0.2em] mb-1">Pick Up Now</span>
                                    <span class="text-xl font-bold text-gray-800 uppercase truncate w-40" x-text="order.nama"></span>
                                </div>
                                <span class="text-5xl font-bold text-emerald-600 tabular-nums tracking-tighter" x-text="'#' + order.id"></span>
                            </div>
                        </template>
                    </div>
                    
                    <template x-if="ready.length === 0">
                        <div class="h-full flex flex-col items-center justify-center text-gray-200">
                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em]">Antrian Kosong</p>
                        </div>
                    </template>
                </div>
            </section>

        </main>

        <!-- FOOTER -->
        <footer class="h-12 bg-white border-t border-gray-100 px-12 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Informasi:</span>
                <div class="w-[600px] overflow-hidden whitespace-nowrap">
                    <marquee class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">
                        Terima kasih telah berkunjung ke {{ config('app.name') }} • Silakan siapkan struk pesanan Anda untuk pengambilan menu.
                    </marquee>
                </div>
            </div>
            <div class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">
                Professional Queue v4.0
            </div>
        </footer>
    </div>

</body>
</html>
