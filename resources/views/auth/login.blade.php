<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - MenuKhas</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        /* Pastikan SweetAlert di atas modal */
        .swal2-container { z-index: 20000 !important; }
    </style>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-gray-50" 
      x-data="{ 
          showAbsensiModal: false,
          pin: '',
          selectedRole: '{{ old('role_check') }}' || null,
          failedAttempts: 0,
          selectRole(role) { this.selectedRole = role; },
          resetRole() { this.selectedRole = null; },
          submitPin() {
              if(this.pin.length < 6) return;
              
              let currentPin = this.pin;
              this.pin = '';
              this.showAbsensiModal = false; // Langsung tutup modal agar tidak menghalangi notifikasi

              fetch('{{ route('absensi.store') }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
                  body: JSON.stringify({ pin: currentPin })
              })
              .then(response => {
                  return response.json().then(data => {
                      if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan sistem');
                      return data;
                  });
              })
              .then(data => {
                  if(data.status == 'success') {
                      this.failedAttempts = 0;
                      Swal.fire({
                          title: 'BERHASIL',
                          text: data.message,
                          icon: 'success',
                          timer: 4000,
                          showConfirmButton: false
                      });
                  } else {
                      throw new Error(data.message || 'Gagal melakukan absensi');
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  this.failedAttempts++;
                  
                  if (this.failedAttempts >= 3) {
                      Swal.fire({
                          title: 'AKSES DITOLAK',
                          text: 'Salah PIN 3x. Hubungi Admin.',
                          icon: 'warning'
                      });
                  } else {
                      Swal.fire({
                          title: 'Gagal',
                          text: error.message,
                          icon: 'error',
                          confirmButtonText: 'Coba Lagi'
                      });
                  }
              });
          }
      }"
      @close-modal.window="showAbsensiModal = false">

    <div class="flex min-h-screen">
        
        <div class="hidden md:flex md:w-1/2 bg-[#5EA5D8] text-white flex-col items-center justify-center p-12 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/3 translate-y-1/3"></div>

            <div class="relative z-10">
                <div class="bg-white/90 rounded-full p-4 mb-6 inline-block shadow-2xl">
                    <div class="bg-[#F0EADA] rounded-full p-8 w-40 h-40 flex items-center justify-center overflow-hidden">
                        <img src="{{ isset($logoPath) && $logoPath ? asset('storage/' . $logoPath) : 'https://i.ibb.co/6r4nCNk/logo-menukhas.png' }}" 
                             alt="Logo Aplikasi" 
                             class="w-full h-full object-contain rounded-full">
                    </div>
                </div>
                <h1 class="text-4xl font-bold mb-2 tracking-wide drop-shadow-md">Selamat Datang</h1>
                <p class="text-lg font-light opacity-90">Solusi Kasir Pintar & Manajemen Bisnis</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 flex items-center justify-center p-6 md:p-12 bg-white">
            <div class="w-full max-w-md">
                
                <div class="md:hidden text-center mb-8">
                    <img src="{{ isset($logoPath) && $logoPath ? asset('storage/' . $logoPath) : 'https://i.ibb.co/6r4nCNk/logo-menukhas.png' }}" 
                         alt="Logo" 
                         class="w-24 h-24 mx-auto object-contain rounded-full shadow-md">
                </div>

                <div x-show="!selectedRole" 
                     x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 scale-95" 
                     x-transition:enter-end="opacity-100 scale-100">
                    
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-gray-800">Siapa Anda?</h2>
                        <p class="text-gray-500 mt-2">Pilih peran Anda untuk melanjutkan</p>
                    </div>

                    <div class="space-y-4">
                        <button @click="selectRole('owner')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-purple-200 hover:bg-white transition-all duration-200 group">
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-700">Owner</h3>
                                <p class="text-xs text-gray-500">Pemilik Toko & Laporan</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-purple-500 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <button @click="selectRole('admin')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-blue-200 hover:bg-white transition-all duration-200 group">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-700">Admin</h3>
                                <p class="text-xs text-gray-500">Manajemen Sistem</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-blue-500 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <button @click="selectRole('kasir')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-green-200 hover:bg-white transition-all duration-200 group">
                            <div class="p-3 bg-green-100 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-green-700">Kasir</h3>
                                <p class="text-xs text-gray-500">Transaksi & Absensi</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-green-500 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div x-show="selectedRole" 
                     x-transition:enter="transition ease-out duration-300 transform" 
                     x-transition:enter-start="opacity-0 translate-x-10" 
                     x-transition:enter-end="opacity-100 translate-x-0" 
                     style="display: none;">
                    
                    <button @click="resetRole()" class="mb-8 flex items-center text-gray-400 hover:text-sky-600 transition text-sm font-medium group">
                        <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Ganti Role
                    </button>

                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800">
                            Login <span class="capitalize text-sky-600" x-text="selectedRole"></span>
                        </h2>
                        <p class="text-gray-500 text-sm mt-2">Masukkan akun anda untuk melanjutkan</p>
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="role_check" :value="selectedRole">

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 transition shadow-sm bg-gray-50 focus:bg-white" 
                                placeholder="email@menukhas.com">
                            @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500 pr-12 transition shadow-sm bg-gray-50 focus:bg-white" 
                                    placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-sky-600 transition">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.592M6.18 6.205A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.97 9.97 0 01-4.138 5.106M3 3l18 18" /></svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#5EA5D8] hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 duration-200 mt-6">
                            Masuk
                        </button>
                    </form>

                    <template x-if="selectedRole === 'kasir'">
                        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                            <p class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-3">Absensi Pegawai</p>
                            <button type="button" 
                                    @click="showAbsensiModal = true; pin = ''; failedAttempts = 0;"
                                    class="flex items-center justify-center w-full py-3 px-4 bg-green-50 text-green-700 border border-green-200 rounded-xl hover:bg-green-100 font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                Absen Masuk (PIN)
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div> 

    <!-- ABSENSI MODAL (Fixed Square Popup) -->
    <div x-show="showAbsensiModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.window="if(showAbsensiModal) $refs.pinInput.focus()">
        
        <!-- Modal Card -->
        <div class="bg-white w-[320px] rounded-3xl shadow-2xl p-8 relative flex flex-col items-center"
             @click.away="showAbsensiModal = false"
             x-show="showAbsensiModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-90 opacity-0"
             x-transition:enter-end="scale-100 opacity-100">
            
            <h3 class="text-gray-800 font-extrabold text-lg mb-2">Verifikasi PIN</h3>
            <p class="text-gray-400 text-xs mb-8">Masukkan 6 digit kode absen</p>

            <!-- Hidden Input -->
            <input type="text" 
                   x-ref="pinInput"
                   x-model="pin" 
                   inputmode="numeric"
                   maxlength="6" 
                   class="absolute opacity-0 w-1 h-1"
                   @input="pin = pin.replace(/\D/g, '').slice(0, 6); if(pin.length === 6) submitPin()"
                   autocomplete="off">

            <!-- PIN Display Boxes (Compact) -->
            <div class="flex gap-2 mb-6" @click="$refs.pinInput.focus()">
                <template x-for="i in 6" :key="i">
                    <div class="w-10 h-12 border-2 rounded-xl flex items-center justify-center text-xl font-bold transition-all duration-200"
                         :class="pin.length >= i 
                            ? 'border-sky-500 bg-sky-50 text-sky-600' 
                            : 'border-gray-200 bg-gray-50 text-gray-300'">
                        <span x-show="pin.length >= i">●</span>
                        <span x-show="pin.length === i - 1" class="w-0.5 h-5 bg-sky-500 animate-pulse"></span>
                    </div>
                </template>
            </div>

            <div class="w-full h-px bg-gray-100 mb-6"></div>

            <button @click="showAbsensiModal = false" 
                    class="text-sm font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                Batal
            </button>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('status') || session('success') || session('toast_success'))
        <div x-data x-init="Toastify({ text: '{{ session('status') ?? session('success') ?? session('toast_success') }}', duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #00b09b, #96c93d)', borderRadius: '12px' } }).showToast();"></div>
    @endif

    @if ($errors->any() && !session('toast_warning'))
        <div x-data x-init="Toastify({ text: '{{ $errors->first() }}', duration: 4000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #ff5f6d, #ffc371)', borderRadius: '12px' } }).showToast();"></div>
    @endif
    
    @if (session('toast_warning'))
        <script>Swal.fire({ icon: 'warning', title: 'Akses Ditolak', text: "{{ session('toast_warning') }}" });</script>
    @endif

    @if (session('session_expired'))
        <div x-data x-init="Toastify({ text: 'Waktu sesi Anda telah habis. Silakan login kembali.', duration: 5000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #f85032, #e73827)', borderRadius: '12px' } }).showToast();"></div>
    @endif

</body>
</html>