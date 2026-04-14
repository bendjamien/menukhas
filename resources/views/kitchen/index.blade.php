<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Terminal - {{ config('app.name', 'MenuKhas') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; color: #111827; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
        .custom-scrollbar-h::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar-h::-webkit-scrollbar-track { background: #f3f4f6; }
        .custom-scrollbar-h::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 20px; }
    </style>
</head>
<body class="h-full overflow-hidden antialiased">
    
    <div class="h-full flex flex-col" 
         x-data="{ 
            orders: [],
            loading: true,
            lastOrderCount: 0,
            isFullscreen: false,
            now: new Date(),
            
            get currentTime() {
                return this.now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            },

            formatTimer(timestamp) {
                const start = new Date(timestamp);
                const diff = Math.abs(this.now - start);
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((diff % (1000 * 60)) / 1000);
                let result = '';
                if (hours > 0) result += hours.toString().padStart(2, '0') + ':';
                result += mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
                return result;
            },

            isOverdue(timestamp) {
                return (this.now - new Date(timestamp)) > 900000;
            },
            
            async fetchOrders() {
                try {
                    let res = await fetch('{{ route('kitchen.api.orders') }}?t=' + new Date().getTime());
                    let data = await res.json();
                    if (data.length > this.lastOrderCount && this.lastOrderCount !== 0) {
                        this.playNotification();
                    }
                    this.orders = data;
                    this.lastOrderCount = data.length;
                } catch (e) { console.error('KDS Error:', e); }
                this.loading = false;
            },
            
            playNotification() {
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                audio.play();
                Toastify({ text: '🔔 Pesanan Baru Masuk', duration: 3000, gravity: 'top', position: 'center', style: { background: '#0284c7', borderRadius: '8px' } }).showToast();
            },

            async updateStatus(orderId, nextStatus) {
                try {
                    let res = await fetch(`/kitchen/${orderId}/status`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ status: nextStatus })
                    });
                    if (res.ok) this.fetchOrders();
                } catch (e) { }
            },

            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    this.isFullscreen = true;
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        this.isFullscreen = false;
                    }
                }
            },

            init() {
                this.fetchOrders();
                setInterval(() => { this.now = new Date() }, 1000);
                setInterval(() => this.fetchOrders(), 5000);
                document.addEventListener('fullscreenchange', () => { this.isFullscreen = !!document.fullscreenElement; });
            }
         }">
        
        <!-- HEADER -->
        <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2 text-gray-400 hover:text-sky-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-sky-600 rounded-lg flex items-center justify-center text-white font-semibold text-sm">
                        {{ substr(config('app.name'), 0, 1) }}
                    </div>
                    <span class="text-lg font-semibold tracking-tight text-gray-800">{{ config('app.name', 'MenuKhas') }}</span>
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-medium rounded border border-gray-200 uppercase">Kitchen</span>
                </div>
                <div class="h-6 w-px bg-gray-200 mx-2"></div>
                <div class="text-lg font-medium text-sky-600 tabular-nums" x-text="currentTime"></div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1 bg-sky-50 rounded-full border border-sky-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-sky-700" x-text="orders.length + ' Aktif'"></span>
                </div>
                <button @click="toggleFullscreen()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <template x-if="!isFullscreen"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></template>
                    <template x-if="isFullscreen"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h4m0 0v4m0-4l-5 5m11-5h4m-4 0v4m0-4l5 5M4 8h4m0 0V4m0 4L3 3m13 5h4m-4 0V4m0 4l5-5"></path></svg></template>
                </button>
            </div>
        </header>

        <!-- MAIN GRID -->
        <main class="flex-grow overflow-x-auto overflow-y-hidden custom-scrollbar-h p-6">
            <div class="flex gap-6 h-full min-w-max">
                <template x-for="order in orders" :key="order.id">
                    <div class="w-80 flex flex-col bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-300">
                        
                        <!-- CARD HEADER -->
                        <div class="p-5 border-b border-gray-100" :class="{
                            'bg-white': order.status_produksi === 'pending',
                            'bg-sky-50/30': order.status_produksi === 'processing',
                            'bg-emerald-50/30': order.status_produksi === 'ready'
                        }">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider" x-text="'#' + order.id"></span>
                                <span class="text-[10px] font-medium text-gray-400" x-text="order.waktu"></span>
                            </div>
                            <h2 class="text-base font-semibold text-gray-800 truncate mb-4" x-text="order.pelanggan"></h2>
                            
                            <!-- TIMER CHIP -->
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg border bg-white shadow-sm"
                                 :class="isOverdue(order.tanggal_raw) ? 'border-red-200 bg-red-50' : 'border-gray-100'">
                                <svg class="w-4 h-4 text-gray-400" :class="isOverdue(order.tanggal_raw) ? 'text-red-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-sm font-semibold tabular-nums" 
                                      :class="isOverdue(order.tanggal_raw) ? 'text-red-600 animate-pulse' : 'text-gray-700'"
                                      x-text="formatTimer(order.tanggal_raw)"></span>
                            </div>
                        </div>

                        <!-- ITEMS -->
                        <div class="flex-grow overflow-y-auto px-5 py-4 space-y-2 custom-scrollbar">
                            <template x-for="item in order.items" :key="item.nama">
                                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                                    <div class="w-7 h-7 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-sky-600 font-bold text-xs shrink-0" x-text="item.jumlah"></div>
                                    <div class="flex-grow min-w-0">
                                        <p class="text-xs font-semibold text-gray-700 leading-tight" x-text="item.nama"></p>
                                        <p class="text-[9px] font-medium text-gray-400 uppercase mt-1" x-text="item.kategori"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- BUTTONS -->
                        <div class="p-4 shrink-0 bg-gray-50 border-t border-gray-100">
                            <template x-if="order.status_produksi === 'pending'">
                                <button @click="updateStatus(order.id, 'processing')" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-xl text-xs transition-all active:scale-95 flex items-center justify-center gap-2">
                                    Mulai Masak
                                </button>
                            </template>
                            <template x-if="order.status_produksi === 'processing'">
                                <button @click="updateStatus(order.id, 'ready')" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs transition-all active:scale-95 flex items-center justify-center gap-2">
                                    Pesanan Siap
                                </button>
                            </template>
                            <template x-if="order.status_produksi === 'ready'">
                                <button @click="updateStatus(order.id, 'completed')" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition-all active:scale-95 flex items-center justify-center gap-2">
                                    Sudah Diambil
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </main>
        
        <footer class="h-8 bg-white border-t border-gray-200 px-6 flex items-center justify-between shrink-0 text-[10px] font-medium text-gray-400">
            <div class="flex items-center gap-4 uppercase tracking-wider">
                <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-gray-200"></span> Pending</div>
                <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Cooking</div>
                <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ready</div>
            </div>
            <div class="uppercase tracking-widest">KDS v4.0 Professional</div>
        </footer>
    </div>
</body>
</html>
