@if(Auth::user()->role == 'admin')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 -m-6 sm:-m-8 border-b border-blue-100 rounded-t-lg">
        <div class="flex items-center">
            <div class="bg-blue-100 rounded-full p-3 mr-4">
                <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Keamanan Password</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui password Anda secara berkala untuk menjaga keamanan akun</p>
            </div>
        </div>
    </div>
<br>
    <div class="mt-8">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('Password Baru')" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Simpan Password') }}</x-primary-button>
            </div>
        </form>
    </div>
@else
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-amber-700">
                    {{ __('Untuk mengubah password, silakan hubungi Administrator.') }}
                </p>
            </div>
        </div>
    </div>
@endif

@if (session('status') === 'password-updated')
    <div 
        x-data 
        x-init="
            Toastify({
                text: '{{ __('Password berhasil diperbarui!') }}',
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