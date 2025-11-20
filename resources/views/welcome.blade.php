<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MenuKhas') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="antialiased text-gray-800">

    <!-- Hero Section -->
    <div class="hero-bg min-h-screen flex items-center justify-center relative">
        <!-- Overlay Gelap -->
        <div class="absolute inset-0 bg-black/40"></div>

        <!-- Kartu Utama -->
        <div class="relative z-10 w-full max-w-4xl mx-4 md:mx-0 flex flex-col md:flex-row glass-card rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Bagian Kiri: Logo & Intro -->
            <div class="w-full md:w-1/2 p-10 md:p-12 flex flex-col justify-center bg-white">
                <div class="mb-6">
                    <!-- Ganti src dengan logo Anda jika ada -->
                    <div class="w-16 h-16 bg-sky-500 rounded-xl flex items-center justify-center shadow-lg mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">MenuKhas</h1>
                    <p class="text-sky-600 font-semibold text-lg">Aplikasi Kasir Pintar & Mudah</p>
                </div>
                
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Kelola transaksi, stok barang, dan laporan penjualan bisnis Anda dengan lebih efisien, cepat, dan akurat dalam satu aplikasi terpadu.
                </p>

                <div class="space-y-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block w-full text-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg shadow-md transition transform hover:scale-105 duration-200">
                                Masuk ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-lg shadow-md transition transform hover:scale-105 duration-200">
                                Login Sekarang
                            </a>
                        @endauth
                    @endif
                    
                    <div class="text-center text-sm text-gray-400 mt-4">
                        &copy; {{ date('Y') }} MenuKhas POS System
                    </div>
                </div>
            </div>

            <!-- Bagian Kanan: Gambar / Ilustrasi -->
            <div class="hidden md:flex w-1/2 bg-sky-50 items-center justify-center p-12 relative overflow-hidden">
                <!-- Dekorasi Lingkaran -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-sky-100 mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-8 left-10 w-72 h-72 rounded-full bg-blue-100 mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                
                <div class="relative z-10 text-center">
                    <img src="https://cdn-icons-png.flaticon.com/512/2942/2942544.png" alt="POS Illustration" class="w-48 mx-auto drop-shadow-xl mb-6 transform hover:-translate-y-2 transition duration-500">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kelola Bisnis Jadi Mudah</h3>
                    <p class="text-gray-600 text-sm">Pantau performa penjualan secara real-time dari mana saja.</p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>