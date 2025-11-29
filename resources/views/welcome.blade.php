<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MenuKhas') }} - Maintenance Mode</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Animasi Blob */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
    </style>
</head>

<body class="antialiased text-gray-800 relative overflow-x-hidden">

    <div class="fixed top-0 right-0 z-50 w-40 h-40 overflow-hidden pointer-events-none">
        <div class="absolute top-[20px] right-[-50px] w-[200px] bg-yellow-400 text-yellow-900 font-bold text-center py-2 transform rotate-45 shadow-lg border-2 border-yellow-200 text-xs uppercase tracking-widest">
            Maintenance
        </div>
    </div>

    <div class="hero-bg min-h-screen flex items-center justify-center relative">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 w-full max-w-4xl mx-4 md:mx-0 flex flex-col md:flex-row glass-card rounded-2xl shadow-2xl overflow-hidden border-t-4 border-yellow-400">

            <div class="w-full md:w-1/2 p-10 md:p-12 flex flex-col justify-center bg-white">
                
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r-lg flex items-start">
                    <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-bold text-yellow-800">System Under Maintenance</h4>
                        <p class="text-xs text-yellow-700 mt-1">Beberapa fitur mungkin sedang diperbaiki.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="w-16 h-16 bg-sky-500 rounded-xl flex items-center justify-center shadow-lg mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">MenuKhas</h1>
                    <p class="text-sky-600 font-semibold text-lg">Aplikasi Kasir Pintar & Mudah</p>
                </div>

                <p class="text-gray-600 mb-8 leading-relaxed">
                    Kelola transaksi, stok barang, dan laporan penjualan bisnis Anda dengan lebih efisien, cepat, dan akurat.
                </p>

                <div class="space-y-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="block w-full text-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg shadow-md transition transform hover:scale-105 duration-200">
                                Masuk ke Dashboard
                            </a>
                        @else
                            <button onclick="togglePinModal()"
                                class="block w-full text-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg shadow-md transition transform hover:scale-105 duration-200">
                                Login Hanya Developer
                            </button>
                        @endauth
                    @endif

                    <a href="https://www.instagram.com/zeinsher?igsh=dThiMmwycHkzdThj&utm_source=qr" class="text-center text-sm text-gray-400 mt-4 block hover:text-sky-600 transition">
                        &copy; {{ date('Y') }} Rubi Dev-MenuKhas
                    </a>
                </div>
            </div>

            <div class="hidden md:flex w-1/2 bg-sky-50 items-center justify-center p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-sky-100 mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-8 left-10 w-72 h-72 rounded-full bg-blue-100 mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>

                <div class="relative z-10 text-center">
                    <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Maintenance Illustration"
                        class="w-48 mx-auto drop-shadow-xl mb-6 transform hover:-translate-y-2 transition duration-500">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sedang Dalam Perbaikan</h3>
                    <p class="text-gray-600 text-sm">Tim kami sedang meningkatkan performa sistem untuk pengalaman yang lebih baik.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="pinModal" class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm transform scale-95 transition-transform duration-300 border-t-4 border-sky-500">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Developer Access</h3>
                <p class="text-sm text-gray-500">Masukkan PIN Keamanan untuk melanjutkan.</p>
            </div>
            
            <div class="mb-4">
                <input type="password" id="pinInput" placeholder="Masukkan PIN..." 
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none text-center text-lg tracking-widest transition"
                    onkeyup="handleEnter(event)">
                <p id="errorMessage" class="text-red-500 text-xs text-center mt-2 hidden">PIN Salah! Akses ditolak.</p>
            </div>

            <div class="flex gap-3">
                <button onclick="togglePinModal()" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition">
                    Batal
                </button>
                <button onclick="checkPin()" class="w-1/2 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-bold shadow-lg transition">
                    Masuk
                </button>
            </div>
        </div>
    </div>

    <script>
        const DEVELOPER_PIN = "10090710**"; 

        const modal = document.getElementById('pinModal');
        const input = document.getElementById('pinInput');
        const errorMsg = document.getElementById('errorMessage');

        function togglePinModal() {
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('div').classList.remove('scale-95');
                    modal.querySelector('div').classList.add('scale-100');
                    input.focus();
                }, 10);
            } else {
                modal.classList.add('opacity-0');
                modal.querySelector('div').classList.remove('scale-100');
                modal.querySelector('div').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    input.value = ''; 
                    errorMsg.classList.add('hidden'); 
                }, 300);
            }
        }

        function checkPin() {
            if (input.value === DEVELOPER_PIN) {
                input.classList.remove('border-red-500', 'focus:ring-red-500');
                input.classList.add('border-green-500', 'focus:ring-green-500');
                window.location.href = "{{ route('login') }}";
            } else {
                input.value = '';
                errorMsg.classList.remove('hidden');
                input.classList.add('border-red-500', 'focus:ring-red-500');
                input.focus();
                
                const box = modal.querySelector('div');
                box.classList.add('translate-x-2');
                setTimeout(() => box.classList.remove('translate-x-2'), 100);
                setTimeout(() => box.classList.add('-translate-x-2'), 200);
                setTimeout(() => box.classList.remove('-translate-x-2'), 300);
            }
        }

        function handleEnter(e) {
            if (e.key === 'Enter') {
                checkPin();
            }
        }
    </script>

</body>
</html>