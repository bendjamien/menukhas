<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- HEADER -->
            <div class="flex items-center gap-4">
                <div class="p-3 bg-sky-100 text-sky-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
                    <p class="text-gray-500 text-sm">Kelola informasi akun dan keamanan Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KOLOM KIRI: KARTU IDENTITAS -->
                <div class="space-y-6">
                    <!-- Kartu Profil -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                        <!-- Header Background -->
                        <div class="h-32 bg-gradient-to-r from-sky-500 to-blue-600"></div>
                        
                        <!-- Avatar Section (Uploadable) -->
                        <div class="relative -mt-12 flex justify-center" x-data="{ photoName: null, photoPreview: null }">
                            <form id="avatar-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                
                                <!-- Hidden fields to pass validation -->
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="username" value="{{ $user->username }}">
                                
                                <input type="file" name="avatar" class="hidden" x-ref="photo"
                                       x-on:change="
                                           photoName = $refs.photo.files[0].name;
                                           const reader = new FileReader();
                                           reader.onload = (e) => {
                                               photoPreview = e.target.result;
                                           };
                                           reader.readAsDataURL($refs.photo.files[0]);
                                           document.getElementById('save-avatar-btn').classList.remove('hidden');
                                       " />

                                <div class="w-24 h-24 bg-white p-1.5 rounded-full shadow-lg relative group cursor-pointer"
                                     x-on:click.prevent="$refs.photo.click()">
                                    
                                    <!-- Foto Profil -->
                                    <div class="w-full h-full rounded-full overflow-hidden border border-sky-100 flex items-center justify-center bg-sky-50">
                                        <!-- Preview (Jika upload baru) -->
                                        <div x-show="photoPreview" style="display: none;">
                                            <span class="block w-full h-full bg-cover bg-no-repeat bg-center"
                                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>
                                        
                                        <!-- Current Avatar (Database) -->
                                        <div x-show="!photoPreview">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-3xl font-black text-sky-600 uppercase tracking-widest">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Overlay Icon Kamera (Hover) -->
                                    <div class="absolute inset-0 bg-black/30 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-200">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22a2 2 0 001.664.89H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                </div>

                                <!-- Tombol Simpan Avatar (Muncul setelah pilih file) -->
                                <button id="save-avatar-btn" type="submit" class="hidden absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-sky-600 text-white text-xs px-3 py-1 rounded-full shadow-md hover:bg-sky-700 transition">
                                    Simpan Foto
                                </button>
                            </form>
                        </div>

                        <!-- Info Section -->
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $user->email }}</p>

                            <!-- Role Badge -->
                            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm border
                                {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-700 border-purple-100' : 
                                  ($user->role === 'kasir' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-200') }}">
                                {{ $user->role }}
                            </div>
                        </div>

                        <!-- Footer Stats -->
                        <div class="bg-gray-50 border-t border-gray-100 p-4 grid grid-cols-2 divide-x divide-gray-200">
                            <div class="text-center">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Bergabung</p>
                                <p class="text-sm font-bold text-gray-700">{{ $user->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Status PIN</p>
                                <div class="flex items-center justify-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $user->pin ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                    <p class="text-sm font-bold {{ $user->pin ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $user->pin ? 'Aktif' : 'Belum Ada' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu PIN Keamanan (Khusus Kasir/Admin) -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">PIN Keamanan</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-4">
                            PIN digunakan untuk akses cepat saat login atau otorisasi di kasir. PIN harus 6 digit angka.
                        </p>

                        @if($user->request_new_pin)
                            <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 text-yellow-800 text-sm mb-4">
                                <p class="font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu Persetujuan Admin
                                </p>
                                <p class="mt-1 text-xs">Anda telah mengajukan perubahan PIN. Harap hubungi Admin untuk konfirmasi.</p>
                            </div>
                        @else
                            <form action="{{ route('profile.request_pin') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">PIN Baru (6 Digit)</label>
                                    <input type="password" name="pin" maxlength="6" pattern="\d*" placeholder="******" class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500 text-center tracking-widest text-lg font-bold" required>
                                    @error('pin', 'userDeletion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition shadow-md">
                                    Ajukan Ganti PIN
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- KOLOM KANAN: FORM UPDATE -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- UPDATE PROFILE -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <h3 class="text-lg font-bold text-gray-800">Edit Informasi Dasar</h3>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Alamat Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600 font-bold"
                                    >{{ __('Tersimpan.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- UPDATE PASSWORD -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <h3 class="text-lg font-bold text-gray-800">Ganti Password</h3>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            @method('put')

                            <div>
                                <x-input-label for="current_password" :value="__('Password Saat Ini')" />
                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <x-primary-button>{{ __('Update Password') }}</x-primary-button>

                                @if (session('status') === 'password-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-green-600 font-bold"
                                    >{{ __('Password Berhasil Diubah.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- DELETE ACCOUNT (OPTIONAL, BIAR LEBIH AMAN BISA DIHAPUS KALO GAK PERLU) -->
                    <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <h3 class="text-lg font-bold text-red-700">Zona Bahaya</h3>
                        </div>
                        <p class="text-sm text-red-600 mb-4">
                            Setelah akun dihapus, semua data dan riwayat akan hilang permanen. Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <x-danger-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        >{{ __('Hapus Akun Saya') }}</x-danger-button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Akun -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Silakan masukkan password Anda untuk konfirmasi penghapusan akun.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Ya, Hapus Akun') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>