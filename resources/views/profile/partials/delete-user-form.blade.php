<section class="space-y-6">
    <!-- 
        Tombol untuk memicu modal
        Diberikan sedikit styling agar lebih terintegrasi dengan desain modern
    -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-red-800">Zona Berbahaya</h3>
                <p class="mt-1 text-sm text-red-600">Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <x-danger-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="shadow-sm"
            >{{ __('Hapus Akun') }}</x-danger-button>
        </div>
    </div>

    <!-- 
        Modal Konfirmasi Hapus Akun (Desain Modern)
    -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="bg-white rounded-xl shadow-2xl overflow-hidden">
            @csrf
            @method('delete')

            <!-- Header Modal dengan Gradient Merah -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                <div class="flex items-center">
                    <!-- Ikon Peringatan dengan Efek Animasi -->
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-white/20 backdrop-blur-sm ring-4 ring-white/30">
                        <svg class="h-7 w-7 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-white">
                            {{ __('Konfirmasi Hapus Akun') }}
                        </h3>
                        <p class="text-red-100 text-sm mt-1">
                            {{ __('Tindakan ini bersifat permanen') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Body Modal -->
            <div class="px-6 py-6">
                <!-- Alert Box Peringatan -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-amber-800">
                                {{ __('Peringatan: Tidak Ada Jalan Kembali') }}
                            </p>
                            <div class="mt-2 text-sm text-amber-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ __('Semua data profil Anda akan dihapus') }}</li>
                                    <li>{{ __('Riwayat transaksi akan hilang') }}</li>
                                    <li>{{ __('Anda tidak akan dapat memulihkan akun ini') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi Konfirmasi -->
                <p class="text-gray-700 mb-6">
                    {{ __('Untuk memverifikasi bahwa Anda adalah pemilik akun, silakan masukkan password Anda di bawah ini.') }}
                </p>

                <!-- Input Password dengan Ikon -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="pl-10 block w-full border-gray-300 focus:ring-red-500 focus:border-red-500 rounded-md"
                            :placeholder="__('Masukkan password Anda')"
                            required
                        />
                    </div>
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>
            </div>

            <!-- Footer Modal dengan Tombol Aksi -->
            <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end sm:space-x-3 rounded-b-xl">
                <x-secondary-button 
                    x-on:click="$dispatch('close')" 
                    class="w-full sm:w-auto justify-center border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-gray-500 transition-colors"
                >
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button 
                    class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-700 focus:ring-red-500 transition-colors shadow-sm"
                >
                    {{ __('Ya, Hapus Akun Saya') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>