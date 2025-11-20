<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MenuKhas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
    <script src="https://unpkg.com/alpinejs" defer></script>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="hidden md:flex md:w-1/2 bg-[#5EA5D8] text-white flex-col items-center justify-center p-12 text-center">
            <div class="bg-white/90 rounded-full p-4 mb-6">
                <div class="bg-[#F0EADA] rounded-full p-8">
                    <img src="https://i.ibb.co/6r4nCNk/logo-menukhas.png" alt="MenuKhas Logo" class="w-32 h-32">
                </div>
            </div>
            <h1 class="text-3xl font-bold mb-2">Selamat Datang</h1>
            <p class="text-lg">Kelola Bisnis Anda Dengan</p>
            <p class="text-lg font-semibold">Aplikasi Menukhas</p>
        </div>

        <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-center mb-2">Masuk</h2>
                <p class="text-gray-600 text-center mb-8">Kamu dapat masuk sebagai Owner Admin ataupun Kasir</p>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Masukkan email Anda">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6" x-data="{ show: false }">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Kata sandi</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" 
                                   placeholder="Masukkan kata sandi">
                            
                            <button type="button" @click="show = !show" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592M6.18 6.205A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.138 5.106M3 3l18 18" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#5DA5D7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600">Masuk</button>
                </form>
            </div>
        </div>
    </div> 

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('status') || session('success') || session('toast_success'))
        <div x-data x-init="
            Toastify({
                text: '{{ session('status') ?? session('success') ?? session('toast_success') }}',
                duration: 3000, 
                gravity: 'top', position: 'right',
                style: { 
                    background: 'linear-gradient(to right, #00b09b, #96c93d)',
                    borderRadius: '8px',
                    boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
                }
            }).showToast();
        "></div>
    @endif

    @if ($errors->any() && !session('toast_warning'))
        <div x-data x-init="
            Toastify({
                text: '{{ $errors->first() }}',
                duration: 4000, 
                gravity: 'top', position: 'right',
                style: { 
                    background: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                    borderRadius: '8px',
                    boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
                }
            }).showToast();
        "></div>
    @endif

    @if (session('toast_warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: "{{ session('toast_warning') }}",
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#f59e0b',
                backdrop: `rgba(0,0,0,0.4)`
            });
        </script>
    @endif

</body>
</html>