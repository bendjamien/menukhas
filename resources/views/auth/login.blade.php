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
        
        /* HIDE DEFAULT ELEMENTS LIBRARY SCANNER */
        #reader__dashboard_section_csr span, 
        #reader__dashboard_section_swaplink { display: none !important; }
        #reader { border: none !important; }
        #reader video { object-fit: cover; height: 100%; width: 100%; border-radius: 12px; }
    </style>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="bg-gray-50" 
      x-data="{ 
          showAbsensiModal: false,
          selectedRole: '{{ old('role_check') }}' || null,
          selectRole(role) { this.selectedRole = role; },
          resetRole() { this.selectedRole = null; }
      }"
      @close-modal.window="showAbsensiModal = false">

    <div class="flex min-h-screen">
        
        <div class="hidden md:flex md:w-1/2 bg-[#5EA5D8] text-white flex-col items-center justify-center p-12 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/3 translate-y-1/3"></div>

            <div class="relative z-10">
                <div class="bg-white/90 rounded-full p-4 mb-6 inline-block shadow-2xl">
                    <div class="bg-[#F0EADA] rounded-full p-6 w-40 h-40 flex items-center justify-center overflow-hidden">
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
                        <button @click="selectRole('owner')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-purple-200 hover:bg-purple-50 transition-all duration-200 group">
                            <div class="p-3 bg-purple-100 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-purple-700">Owner</h3>
                                <p class="text-xs text-gray-500">Pemilik Toko & Laporan</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-purple-500 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <button @click="selectRole('admin')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-blue-200 hover:bg-blue-50 transition-all duration-200 group">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-700">Admin</h3>
                                <p class="text-xs text-gray-500">Manajemen Sistem</p>
                            </div>
                            <svg class="w-5 h-5 ml-auto text-gray-300 group-hover:text-blue-500 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <button @click="selectRole('kasir')" class="w-full bg-white border border-gray-100 p-4 rounded-2xl flex items-center shadow-sm hover:shadow-lg hover:border-green-200 hover:bg-green-50 transition-all duration-200 group">
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

                        <button type="submit" class="w-full bg-[#5EA5D8] hover:bg-sky-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 duration-200">
                            Masuk
                        </button>
                    </form>

                    <template x-if="selectedRole === 'kasir'">
                        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                            <p class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-3">Absensi Pegawai</p>
                            <button type="button" 
                                    @click="showAbsensiModal = true; startScanner()"
                                    class="flex items-center justify-center w-full py-3 px-4 bg-green-50 text-green-700 border border-green-200 rounded-xl hover:bg-green-100 font-bold transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                Scan Absensi (Masuk / Pulang)
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div> 

    <div x-show="showAbsensiModal" style="display: none;" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center px-4 pb-6 sm:pb-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showAbsensiModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
        <div x-show="showAbsensiModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-10 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-10 sm:scale-95" class="bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden w-full max-w-md relative z-10">
            <div class="px-6 py-4 bg-white flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Scanner Absensi</h3>
                    <p class="text-xs text-gray-500">Pastikan QR Code terlihat jelas</p>
                </div>
                <button @click="closeModal()" class="bg-gray-100 rounded-full p-2 text-gray-500 hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 bg-gray-50">
                <div class="relative w-full aspect-square bg-gray-900 rounded-2xl overflow-hidden shadow-inner border border-gray-200">
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white/50 z-0">
                        <svg class="animate-spin h-10 w-10 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm font-medium">Menyiapkan Kamera...</p>
                    </div>
                    <div id="reader" class="relative z-10 w-full h-full object-cover"></div>
                    <div class="absolute inset-0 pointer-events-none z-20 p-8 flex items-center justify-center">
                        <div class="w-full h-full border-2 border-dashed border-white/70 rounded-xl relative">
                            <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-green-500 rounded-tl-lg -mt-1 -ml-1"></div>
                            <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-green-500 rounded-tr-lg -mt-1 -mr-1"></div>
                            <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-green-500 rounded-bl-lg -mb-1 -ml-1"></div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-green-500 rounded-br-lg -mb-1 -mr-1"></div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-sm font-medium text-gray-600 mt-4 bg-white py-3 rounded-xl shadow-sm border border-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22a2 2 0 001.664.89H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Arahkan QR Code ke dalam bingkai
                </p>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let html5QrcodeScanner = null;
        
        function closeModal() {
            window.dispatchEvent(new CustomEvent('close-modal'));
            stopScanner();
        }

        function startScanner() {
            // Hapus delay agar spinner muncul langsung, lalu kamera menyusul
            if(html5QrcodeScanner === null) {
                let config = { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    rememberLastUsedCamera: true 
                };
                html5QrcodeScanner = new Html5QrcodeScanner("reader", config, /* verbose= */ false);
            }
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function stopScanner() {
            if(html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => console.error("Failed to clear.", error));
            }
        }

        function onScanFailure(error) {}

        function onScanSuccess(decodedText, decodedResult) {
            html5QrcodeScanner.pause();

            fetch("{{ route('absensi.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ qr_code: decodedText })
            })
            .then(response => {
                if (!response.ok) throw new Error('Server Error');
                return response.json();
            })
            .then(data => {
                if(data.status == 'success') {
                    closeModal(); 
                    Swal.fire({
                        title: data.tipe === 'masuk' ? 'SELAMAT DATANG' : 'HATI-HATI',
                        text: data.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        backdrop: `rgba(0,0,123,0.4)`
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: data.message,
                        icon: 'error',
                        timer: 2000
                    }).then(() => {
                        html5QrcodeScanner.resume();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                html5QrcodeScanner.resume();
            });
        }
    </script>

    @if (session('status') || session('success') || session('toast_success'))
        <div x-data x-init="Toastify({ text: '{{ session('status') ?? session('success') ?? session('toast_success') }}', duration: 3000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #00b09b, #96c93d)', borderRadius: '12px' } }).showToast();"></div>
    @endif

    @if ($errors->any() && !session('toast_warning'))
        <div x-data x-init="Toastify({ text: '{{ $errors->first() }}', duration: 4000, gravity: 'top', position: 'right', style: { background: 'linear-gradient(to right, #ff5f6d, #ffc371)', borderRadius: '12px' } }).showToast();"></div>
    @endif
    
    @if (session('toast_warning'))
        <script>Swal.fire({ icon: 'warning', title: 'Akses Ditolak', text: "{{ session('toast_warning') }}" });</script>
    @endif

</body>
</html>