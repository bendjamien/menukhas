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
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div class="hidden md:flex md:w-1/2 bg-[#5EA5D8] text-white flex-col items-center justify-center p-12 text-center">
            <div class="bg-white/90 rounded-full p-4 mb-6">
                <div class="bg-[#F0EADA] rounded-full p-8">
                    <img src="https://i.ibb.co/6r4nCNk/logo-menukhas.png" alt="MenuKhas Logo" class="w-32 h-32">
                </div>
            </div>
            <h1 class="text-3xl font-bold mb-2">Selamat Datang Diaplikasi</h1>
            <p class="text-lg">Optimalkan Bisnis Anda Dengan</p>
            <p class="text-lg font-semibold">Aplikasi Menukhas</p>
        </div>
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12">
            <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold text-center mb-2">Masuk</h2>
                <p class="text-gray-600 text-center mb-8">Kamu dapat masuk sebagai Owner ataupun staff</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan email Anda">
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Kata sandi</label>
                        <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan kata sandi">
                    </div>
                    <button type="submit" class="w-full bg-[#5DA5D7] text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600">Masuk</button>
                </form>
            </div>
        </div>
    </div> @if (session('success'))
        <div x-data x-init="
            Toastify({
                text: '{{ session('success') }}',
                duration: 3000, gravity: 'top', position: 'right',
                style: { background: 'linear-gradient(to right, #00b09b, #96c93d)' }
            }).showToast();
        "></div>
    @endif

    @if ($errors->any())
        <div x-data x-init="
            Toastify({
                text: 'Email atau password salah. Silakan coba lagi.',
                duration: 3000, gravity: 'top', position: 'right',
                style: { background: 'linear-gradient(to right, #ff5f6d, #ffc371)' }
            }).showToast();
        "></div>
    @endif

</body>
</html>