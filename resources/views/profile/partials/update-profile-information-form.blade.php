<section>
    <!-- 
        Card Container untuk Form Update Profil
        Desain modern dengan shadow dan border radius
    -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-blue-100">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Informasi Profil</h2>
                    <p class="mt-1 text-sm text-gray-600">Perbarui informasi dasar profil Anda</p>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="p-6 sm:p-8">
            <form method="post" action="{{ route('profile.update') }}" class="space-y-6" x-data="profileForm()">
                @csrf
                @method('patch')

                <!-- Name Field -->
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="pl-10 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md" 
                            :value="old('name', $user->name)" 
                            required 
                            autofocus 
                            autocomplete="name"
                            x-model="name"
                        />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Email Field -->
                <div>
                    <x-input-label for="email" :value="__('Alamat Email')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="pl-10 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md" 
                            :value="old('email', $user->email)" 
                            required 
                            autocomplete="username"
                            x-model="email"
                        />
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    <!-- Email Verification Section -->
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800">
                                        {{ __('Email Belum Diverifikasi') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-amber-700">
                                        <p>
                                            {{ __('Alamat email Anda belum diverifikasi. ') }}
                                            <button 
                                                form="send-verification" 
                                                class="font-medium underline hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 rounded-md"
                                            >
                                                {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                                            </button>
                                        </p>
                                        
                                        @if (session('status') === 'verification-link-sent')
                                            <div class="mt-2 flex items-center">
                                                <svg class="h-4 w-4 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="font-medium">
                                                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Verified Email Badge -->
                        <div class="mt-2 flex items-center">
                            <svg class="h-4 w-4 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm text-green-600 font-medium">{{ __('Email Terverifikasi') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Submit Button with Loading State -->
                <div class="flex items-center justify-end pt-4">
                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        
                        <svg x-show="isSubmitting" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Email Verification -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    <!-- 
        Notifikasi Sukses dengan Toastify
        Dipindahkan ke luar form untuk memastikan muncul bahkan setelah form di-reset
    -->
    @if (session('status') === 'profile-updated')
        <div 
            x-data 
            x-init="
                Toastify({
                    text: '{{ __('Profil berhasil diperbarui!') }}',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    style: {
                        background: 'linear-gradient(to right, #10b981, #059669)',
                        'border-radius': '0.5rem',
                        'box-shadow': '0 10px 25px rgba(0, 0, 0, 0.1)'
                    }
                }).showToast();
            "
        >
        </div>
    @endif
</section>

<!-- Alpine.js Component untuk Profile Form -->
<script>
function profileForm() {
    return {
        name: '{{ $user->name }}',
        email: '{{ $user->email }}',
        isSubmitting: false,
        
        init() {
            // Add form submit handler
            const form = document.querySelector('form[method="post"]');
            if (form) {
                form.addEventListener('submit', () => {
                    this.isSubmitting = true;
                });
            }
        }
    }
}
</script>