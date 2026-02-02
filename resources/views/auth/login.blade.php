<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - MenuKhas POS</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        /* Hide scrollbar */
        ::-webkit-scrollbar { width: 0px; background: transparent; }
        
        /* Custom Blue Gradient Background for Left Side */
        .bg-brand-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-white h-screen overflow-hidden"
      x-data="{ 
          showAbsensiModal: false,
          pin: '',
          isLoading: false,
          failedAttempts: 0,
          submitPin() {
              if(this.pin.length < 6) return;
              let currentPin = this.pin;
              this.pin = '';
              this.showAbsensiModal = false;

              fetch('{{ route('absensi.store') }}', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                  body: JSON.stringify({ pin: currentPin })
              })
              .then(response => response.json().then(data => ({ status: response.status, body: data })))
              .then(({ status, body }) => {
                  if (status >= 200 && status < 300 && body.status == 'success') {
                      Swal.fire({ title: 'Berhasil', text: body.message, icon: 'success', timer: 2000, showConfirmButton: false, confirmButtonColor: '#3b82f6' });
                  } else {
                      throw new Error(body.message || 'Gagal');
                  }
              })
              .catch(error => {
                  Swal.fire({ title: 'Gagal', text: error.message, icon: 'error', confirmButtonText: 'Coba Lagi', confirmButtonColor: '#ef4444' });
              });
          }
      }">

    <div class="flex h-full w-full">
        
        <!-- LEFT SIDE: Blue Theme & Branding -->
        <div class="hidden lg:flex w-2/3 relative bg-brand-gradient items-center justify-center overflow-hidden">
            <!-- Background Image with Blue Overlay -->
            <div class="absolute inset-0 mix-blend-overlay opacity-20">
                <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?q=80&w=2070&auto=format&fit=crop" 
                     alt="Restaurant Ambience" 
                     class="w-full h-full object-cover grayscale">
            </div>
            
            <!-- Decorative Circles -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-300 opacity-10 rounded-full translate-x-1/3 translate-y-1/3 blur-3xl"></div>

            <!-- Content -->
            <div class="relative z-10 px-20 max-w-4xl text-center">
                <div class="mb-8 flex justify-center">
                    <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                         <img src="{{ isset($logoPath) && $logoPath ? asset('storage/' . $logoPath) : 'https://i.ibb.co/6r4nCNk/logo-menukhas.png' }}" 
                             alt="Logo" class="w-12 h-12 object-contain brightness-0 invert drop-shadow-md">
                    </div>
                </div>
                <h1 class="text-4xl xl:text-5xl font-bold text-white tracking-tight mb-6 animate-fade-in drop-shadow-sm">
                    Excellence in Every Transaction.
                </h1>
                <p class="text-lg text-blue-100 font-light leading-relaxed max-w-2xl mx-auto animate-fade-in" style="animation-delay: 0.1s;">
                    Sistem manajemen restoran terpadu untuk efisiensi operasional, 
                    analisis penjualan, dan pengalaman pelanggan yang lebih baik.
                </p>
            </div>
            
            <div class="absolute bottom-8 text-blue-200/60 text-xs tracking-widest uppercase font-medium">
                Powered by MenuKhas System
            </div>
        </div>

        <!-- RIGHT SIDE: Login Form -->
        <div class="w-full lg:w-1/3 h-full bg-white flex flex-col relative shadow-2xl z-20">
            
            <!-- Main Content Center -->
            <div class="flex-1 flex flex-col justify-center px-8 sm:px-12 md:px-16">
                
                <div class="w-full max-w-sm mx-auto animate-fade-in">
                    <!-- Mobile Logo -->
                    <div class="lg:hidden mb-8 text-center">
                        <img src="{{ isset($logoPath) && $logoPath ? asset('storage/' . $logoPath) : 'https://i.ibb.co/6r4nCNk/logo-menukhas.png' }}" 
                             alt="Logo" class="w-16 h-16 mx-auto object-contain">
                    </div>

                    <div class="mb-10">
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Datang</h2>
                        <p class="text-slate-500 text-sm mt-2">Silakan masuk untuk melanjutkan</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="isLoading = true">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Email</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-slate-400" 
                                    placeholder="name@menukhas.com">
                            </div>
                            @error('email') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Password</label>
                            </div>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </span>
                                <input :type="show ? 'text' : 'password'" name="password" required 
                                    class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-slate-400" 
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592M6.18 6.205A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.138 5.106M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                                <span class="ml-2 text-sm text-slate-600 group-hover:text-blue-600 transition">Ingat saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">Lupa Password?</a>
                            @endif
                        </div>

                        <button type="submit" 
                                :disabled="isLoading"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 px-4 rounded-lg shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-200 flex items-center justify-center gap-2 mt-4 transform hover:-translate-y-0.5">
                            <span x-text="isLoading ? 'Memproses...' : 'Masuk Sekarang'"></span>
                            <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer: Absensi Button (Sticky Bottom) -->
            <div class="p-6 bg-slate-50 border-t border-slate-100">
                <button type="button" 
                        @click="showAbsensiModal = true; pin = ''; failedAttempts = 0;"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 font-medium hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition-all text-sm group">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Absensi Pegawai (PIN)
                </button>
            </div>
        </div>
    </div> 

    <!-- ABSENSI MODAL (Blue Themed) -->
    <div x-show="showAbsensiModal" 
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm"
         @keydown.window="if(showAbsensiModal) $refs.pinInput.focus()">
        
        <div class="bg-white w-full max-w-[340px] rounded-2xl shadow-2xl p-6 relative flex flex-col items-center animate-fade-in"
             @click.away="showAbsensiModal = false">
            
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.848.578-4.13m4.832 1.474a9 9 0 014.824-2.435"></path></svg>
            </div>

            <h3 class="text-lg font-bold text-slate-900 mb-1">Absensi Pegawai</h3>
            <p class="text-slate-500 text-xs mb-6">Masukkan 6-digit PIN keamanan</p>

            <input type="text" x-ref="pinInput" x-model="pin" inputmode="numeric" maxlength="6" 
                   class="absolute opacity-0 w-1 h-1"
                   @input="pin = pin.replace(/\D/g, '').slice(0, 6); if(pin.length === 6) submitPin()"
                   autocomplete="off">

            <div class="flex gap-2 mb-8 w-full justify-center" @click="$refs.pinInput.focus()">
                <template x-for="i in 6" :key="i">
                    <div class="w-10 h-12 border rounded-lg flex items-center justify-center text-xl font-bold transition-all duration-200 cursor-text"
                         :class="pin.length >= i ? 'border-blue-600 bg-blue-600 text-white shadow-md shadow-blue-200' : 'border-slate-200 bg-slate-50 text-slate-300'">
                        <span x-show="pin.length >= i">•</span>
                    </div>
                </template>
            </div>

            <button @click="showAbsensiModal = false" class="text-xs font-semibold text-slate-400 hover:text-red-500 uppercase tracking-widest transition-colors">
                Batal
            </button>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    @if (session('status') || session('success') || session('toast_success'))
        <div x-data x-init="Toastify({ text: '{{ session('status') ?? session('success') ?? session('toast_success') }}', duration: 3000, gravity: 'top', position: 'right', style: { background: '#2563EB', borderRadius: '8px', boxShadow: 'none' } }).showToast();"></div>
    @endif

    @if ($errors->any() && !session('toast_warning'))
        <div x-data x-init="Toastify({ text: '{{ $errors->first() }}', duration: 4000, gravity: 'top', position: 'right', style: { background: '#EF4444', borderRadius: '8px' } }).showToast();"></div>
    @endif

    @if (session('toast_warning'))
        <script>Swal.fire({ icon: 'warning', title: 'Perhatian', text: "{{ session('toast_warning') }}", confirmButtonColor: '#2563EB' });</script>
    @endif

</body>
</html>