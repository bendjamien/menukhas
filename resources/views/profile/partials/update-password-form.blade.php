<section class="space-y-6">
    <!-- 
        Card Container untuk Form Update Password
        Desain modern dengan shadow dan border radius
    -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-blue-100">
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

        <!-- Form Container -->
        <div class="p-6 sm:p-8">
            <form method="post" action="{{ route('password.update') }}" class="space-y-6" x-data="passwordForm()">
                @csrf
                @method('put')

                <!-- Current Password Field -->
                <div>
                    <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="update_password_current_password" 
                            name="current_password" 
                            type="password" 
                            class="pl-10 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md" 
                            autocomplete="current-password"
                            x-model="currentPassword"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="togglePasswordVisibility('current')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg x-show="!showCurrentPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="showCurrentPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <!-- New Password Field -->
                <div>
                    <x-input-label for="update_password_password" :value="__('New Password')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="update_password_password" 
                            name="password" 
                            type="password" 
                            class="pl-10 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md" 
                            autocomplete="new-password"
                            x-model="newPassword"
                            @input="checkPasswordStrength"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="togglePasswordVisibility('new')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg x-show="!showNewPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="showNewPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div class="mt-2" x-show="newPassword">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-500">Kekuatan Password:</span>
                            <span class="text-xs font-medium" :class="passwordStrengthColor" x-text="passwordStrengthText"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300" :class="passwordStrengthColor" :style="`width: ${passwordStrengthPercentage}%`"></div>
                        </div>
                    </div>
                    
                    <!-- Password Requirements -->
                    <div class="mt-3 p-3 bg-gray-50 rounded-md">
                        <p class="text-xs font-medium text-gray-700 mb-2">Password harus memenuhi:</p>
                        <ul class="space-y-1">
                            <li class="flex items-start">
                                <svg x-show="passwordRequirements.length" class="h-4 w-4 text-green-500 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="!passwordRequirements.length" class="h-4 w-4 text-gray-400 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs text-gray-600">Minimal 8 karakter</span>
                            </li>
                            <li class="flex items-start">
                                <svg x-show="passwordRequirements.uppercase" class="h-4 w-4 text-green-500 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="!passwordRequirements.uppercase" class="h-4 w-4 text-gray-400 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs text-gray-600">Setidaknya satu huruf besar</span>
                            </li>
                            <li class="flex items-start">
                                <svg x-show="passwordRequirements.number" class="h-4 w-4 text-green-500 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="!passwordRequirements.number" class="h-4 w-4 text-gray-400 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs text-gray-600">Setidaknya satu angka</span>
                            </li>
                            <li class="flex items-start">
                                <svg x-show="passwordRequirements.special" class="h-4 w-4 text-green-500 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="!passwordRequirements.special" class="h-4 w-4 text-gray-400 mr-1 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs text-gray-600">Setidaknya satu karakter khusus</span>
                            </li>
                        </ul>
                    </div>
                    
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input 
                            id="update_password_password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="pl-10 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md" 
                            autocomplete="new-password"
                            x-model="confirmPassword"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="togglePasswordVisibility('confirm')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg x-show="!showConfirmPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="showConfirmPassword" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Match Indicator -->
                    <div class="mt-2" x-show="confirmPassword">
                        <div class="flex items-center">
                            <svg x-show="passwordsMatch" class="h-4 w-4 text-green-500 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <svg x-show="!passwordsMatch" class="h-4 w-4 text-red-500 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs" :class="passwordsMatch ? 'text-green-600' : 'text-red-600'" x-text="passwordsMatch ? 'Password cocok' : 'Password tidak cocok'"></span>
                        </div>
                    </div>
                    
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit Button with Loading State -->
                <div class="flex items-center justify-end pt-4">
                    <button 
                        type="submit" 
                        :disabled="isSubmitting || !passwordsMatch"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        
                        <svg x-show="isSubmitting" class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Perbarui Password'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 
        Notifikasi Sukses dengan Toastify
        Dipindahkan ke luar form untuk memastikan muncul bahkan setelah form di-reset
    -->
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
</section>

<!-- Alpine.js Component untuk Password Form -->
<script>
function passwordForm() {
    return {
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
        showCurrentPassword: false,
        showNewPassword: false,
        showConfirmPassword: false,
        isSubmitting: false,
        passwordStrength: 0,
        passwordStrengthText: '',
        passwordStrengthColor: '',
        passwordStrengthPercentage: 0,
        passwordRequirements: {
            length: false,
            uppercase: false,
            number: false,
            special: false
        },
        
        get passwordsMatch() {
            return this.newPassword === this.confirmPassword && this.newPassword !== '';
        },
        
        togglePasswordVisibility(field) {
            if (field === 'current') {
                this.showCurrentPassword = !this.showCurrentPassword;
                document.getElementById('update_password_current_password').type = this.showCurrentPassword ? 'text' : 'password';
            } else if (field === 'new') {
                this.showNewPassword = !this.showNewPassword;
                document.getElementById('update_password_password').type = this.showNewPassword ? 'text' : 'password';
            } else if (field === 'confirm') {
                this.showConfirmPassword = !this.showConfirmPassword;
                document.getElementById('update_password_password_confirmation').type = this.showConfirmPassword ? 'text' : 'password';
            }
        },
        
        checkPasswordStrength() {
            const password = this.newPassword;
            
            // Check requirements
            this.passwordRequirements.length = password.length >= 8;
            this.passwordRequirements.uppercase = /[A-Z]/.test(password);
            this.passwordRequirements.number = /[0-9]/.test(password);
            this.passwordRequirements.special = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            
            // Calculate strength
            let strength = 0;
            if (this.passwordRequirements.length) strength += 25;
            if (this.passwordRequirements.uppercase) strength += 25;
            if (this.passwordRequirements.number) strength += 25;
            if (this.passwordRequirements.special) strength += 25;
            
            this.passwordStrength = strength;
            this.passwordStrengthPercentage = strength;
            
            // Set text and color based on strength
            if (strength === 0) {
                this.passwordStrengthText = '';
                this.passwordStrengthColor = '';
            } else if (strength <= 25) {
                this.passwordStrengthText = 'Lemah';
                this.passwordStrengthColor = 'bg-red-500 text-red-600';
            } else if (strength <= 50) {
                this.passwordStrengthText = 'Sedang';
                this.passwordStrengthColor = 'bg-yellow-500 text-yellow-600';
            } else if (strength <= 75) {
                this.passwordStrengthText = 'Kuat';
                this.passwordStrengthColor = 'bg-blue-500 text-blue-600';
            } else {
                this.passwordStrengthText = 'Sangat Kuat';
                this.passwordStrengthColor = 'bg-green-500 text-green-600';
            }
        },
        
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