<x-app-layout>
    <div class="h-[calc(100vh-2rem)] flex flex-col" x-data="{
        step: 1,
        metode: 'email',
        nama: '',
        target: '',
        otp: '',
        loading: false,
        message: '',
        error: '',

        async sendOTP() {
            this.loading = true;
            this.error = '';
            try {
                let res = await fetch('{{ route('member.registration.send_otp') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ nama: this.nama, metode: this.metode, target: this.target })
                });
                let data = await res.json();
                if (res.ok) {
                    this.step = 2;
                    Toastify({ text: data.message, duration: 3000, style: { background: '#10b981' } }).showToast();
                } else {
                    this.error = data.message;
                }
            } catch (e) { this.error = 'Terjadi kesalahan sistem.'; }
            this.loading = false;
        },

        async verifyOTP() {
            this.loading = true;
            this.error = '';
            try {
                let res = await fetch('{{ route('member.registration.verify_otp') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ target: this.target, otp: this.otp })
                });
                let data = await res.json();
                if (res.ok) {
                    this.step = 3;
                    Toastify({ text: data.message, duration: 5000, style: { background: '#10b981' } }).showToast();
                } else {
                    this.error = data.message;
                }
            } catch (e) { this.error = 'Kode verifikasi salah atau expired.'; }
            this.loading = false;
        }
    }">

        <!-- Header Section (Full Width) -->
        <div class="bg-white border-b border-gray-100 px-8 py-6 mb-6 rounded-3xl shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-gray-800 tracking-tight">Pendaftaran Member Baru</h1>
                    <p class="text-sm text-gray-500 mt-1">Verifikasi data pelanggan dan kirim kartu digital secara otomatis.</p>
                </div>
                
                <!-- Stepper Desktop -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div :class="step >= 1 ? 'bg-sky-600 text-white' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-sm">1</div>
                        <span class="text-xs font-bold text-gray-400">Data</span>
                    </div>
                    <div class="w-8 h-px bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div :class="step >= 2 ? 'bg-sky-600 text-white' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-sm">2</div>
                        <span class="text-xs font-bold text-gray-400">Verifikasi</span>
                    </div>
                    <div class="w-8 h-px bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div :class="step >= 3 ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400'" class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-sm">3</div>
                        <span class="text-xs font-bold text-gray-400">Selesai</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (Full Height Balanced) -->
        <div class="flex-grow bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left Side: Visual/Info -->
            <div class="w-full md:w-1/3 bg-sky-50 p-8 flex flex-col justify-center border-r border-gray-100">
                <div class="max-w-xs mx-auto text-center md:text-left">
                    <div class="inline-flex p-4 bg-white rounded-3xl shadow-xl shadow-sky-200/50 text-sky-600 mb-6">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-extrabold text-sky-900 leading-tight mb-4">Loyalty Program {{ config('app.name') }}</h2>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-sm text-sky-700">
                            <svg class="w-5 h-5 text-sky-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Kumpulkan poin setiap belanja
                        </li>
                        <li class="flex items-center gap-3 text-sm text-sky-700">
                            <svg class="w-5 h-5 text-sky-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Dapatkan promo eksklusif member
                        </li>
                        <li class="flex items-center gap-3 text-sm text-sky-700">
                            <svg class="w-5 h-5 text-sky-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Kartu member digital via WA/Email
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side: Form Content -->
            <div class="flex-grow p-8 md:p-12 flex flex-col justify-center overflow-y-auto custom-scrollbar">
                <div class="max-w-md mx-auto w-full">
                    
                    <!-- STEP 1: FORM INPUT -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800">Lengkapi Data Member</h3>
                            <p class="text-sm text-gray-400">Pilih metode kirim kartu yang diinginkan.</p>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="metode" value="email" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-2xl text-center transition-all peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 border-gray-100 text-gray-400 bg-gray-50/50">
                                        <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-bold uppercase tracking-widest">Email</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" x-model="metode" value="whatsapp" class="peer sr-only">
                                    <div class="p-4 border-2 rounded-2xl text-center transition-all peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:text-sky-700 border-gray-100 text-gray-400 bg-gray-50/50">
                                        <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <span class="text-xs font-bold uppercase tracking-widest">WhatsApp</span>
                                    </div>
                                </label>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Nama Lengkap</label>
                                <input type="text" x-model="nama" placeholder="Masukkan nama pelanggan..." class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-sky-500 py-4 px-5 font-medium text-gray-800 transition-all">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1" x-text="metode === 'email' ? 'Alamat Email Aktif' : 'Nomor WhatsApp Aktif'"></label>
                                <input :type="metode === 'email' ? 'email' : 'tel'" x-model="target" :placeholder="metode === 'email' ? 'contoh@mail.com' : '08xxxxxxxxxx'" class="w-full bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-sky-500 py-4 px-5 font-medium text-gray-800 transition-all">
                            </div>

                            <div x-show="error" class="p-4 bg-red-50 text-red-600 text-sm rounded-2xl border border-red-100" x-text="error"></div>

                            <button @click="sendOTP()" :disabled="loading || !nama || !target" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-sky-500/20 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                                <span x-show="!loading">Lanjutkan Verifikasi</span>
                                <div x-show="loading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: OTP VERIFICATION -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                        <div class="text-center mb-8">
                            <div class="inline-flex p-4 bg-sky-50 text-sky-600 rounded-3xl mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Verifikasi Keamanan</h3>
                            <p class="text-sm text-gray-400 mt-2 leading-relaxed">Masukkan 6 digit kode yang dikirim ke <br> <span class="font-bold text-gray-700" x-text="target"></span></p>
                        </div>

                        <div class="space-y-6">
                            <input type="text" x-model="otp" maxlength="6" class="w-full text-center text-4xl font-black tracking-[0.5em] border-none bg-gray-50 rounded-3xl focus:ring-2 focus:ring-sky-500 py-6" placeholder="------">
                            
                            <div x-show="error" class="p-4 bg-red-50 text-red-600 text-sm rounded-2xl border border-red-100" x-text="error"></div>

                            <button @click="verifyOTP()" :disabled="loading || otp.length < 6" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-sky-500/20 transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                                <span x-show="!loading">Aktifkan Sekarang</span>
                                <div x-show="loading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            </button>

                            <button @click="step = 1" class="w-full text-gray-400 hover:text-gray-600 text-xs font-bold uppercase tracking-widest transition-colors">Kembali & Ubah Data</button>
                        </div>
                    </div>

                    <!-- STEP 3: SUCCESS -->
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-500 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" style="display: none;">
                        <div class="text-center py-6">
                            <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-100">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h2 class="text-3xl font-black text-gray-900 mb-2">Member Aktif!</h2>
                            <p class="text-sm text-gray-500 mb-10 px-4">Selamat! <strong class="text-gray-800" x-text="nama"></strong> telah terdaftar secara resmi. Kartu member digital telah dikirimkan ke <span x-text="metode"></span>.</p>
                            
                            <div class="grid grid-cols-1 gap-3">
                                <a href="{{ route('pos.index') }}" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-sky-500/20 transition-all flex items-center justify-center gap-2">
                                    Mulai Transaksi Baru
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </a>
                                <button @click="window.location.reload()" class="w-full bg-gray-50 text-gray-500 hover:bg-gray-100 font-bold py-4 rounded-2xl transition-all">Daftarkan Member Lain</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
