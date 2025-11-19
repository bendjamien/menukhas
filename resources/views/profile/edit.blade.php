<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
        
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-6 mb-8 shadow-lg">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">
                Pengaturan Profil
            </h1>
            <p class="mt-2 text-blue-100 max-w-2xl">
                Kelola informasi profil, keamanan akun, dan preferensi Anda
            </p>
        </div>
        <div x-data="{ activeTab: 'info' }" class="lg:grid lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-3 mb-6 lg:mb-0">
                <div class="hidden lg:block bg-white rounded-xl shadow-sm p-2">
                    <button @click="activeTab = 'info'"
                            :class="{ 
                                'bg-blue-50 text-blue-700 border-r-2 border-blue-700': activeTab === 'info', 
                                'text-gray-600 hover:bg-gray-50 hover:text-gray-900': activeTab !== 'info' 
                            }"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Informasi Profil</span>
                    </button>
                    <button @click="activeTab = 'password'"
                            :class="{ 
                                'bg-blue-50 text-blue-700 border-r-2 border-blue-700': activeTab === 'password', 
                                'text-gray-600 hover:bg-gray-50 hover:text-gray-900': activeTab !== 'password' 
                            }"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Ubah Password</span>
                    </button>
                    <button @click="activeTab = 'delete'"
                            :class="{ 
                                'bg-red-50 text-red-700 border-r-2 border-red-700': activeTab === 'delete', 
                                'text-gray-600 hover:bg-gray-50 hover:text-gray-900': activeTab !== 'delete' 
                            }"
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Hapus Akun</span>
                    </button>
                </div>

                <div class="lg:hidden flex space-x-2 overflow-x-auto pb-2 -mx-4 px-4">
                    <button @click="activeTab = 'info'"
                            :class="{ 
                                'bg-blue-600 text-white': activeTab === 'info', 
                                'bg-white text-gray-600 border border-gray-200': activeTab !== 'info' 
                            }"
                            class="flex items-center space-x-2 px-4 py-2.5 rounded-lg transition-colors duration-200 focus:outline-none whitespace-nowrap">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                        </svg>
                        <span>Profil</span>
                    </button>

                    <button @click="activeTab = 'password'"
                            :class="{ 
                                'bg-blue-600 text-white': activeTab === 'password', 
                                'bg-white text-gray-600 border border-gray-200': activeTab !== 'password' 
                            }"
                            class="flex items-center space-x-2 px-4 py-2.5 rounded-lg transition-colors duration-200 focus:outline-none whitespace-nowrap">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                        </svg>
                        <span>Password</span>
                    </button>

                    <button @click="activeTab = 'delete'"
                            :class="{ 
                                'bg-red-600 text-white': activeTab === 'delete', 
                                'bg-white text-gray-600 border border-gray-200': activeTab !== 'delete' 
                            }"
                            class="flex items-center space-x-2 px-4 py-2.5 rounded-lg transition-colors duration-200 focus:outline-none whitespace-nowrap">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>

            <div class="lg:col-span-9">
                <div x-show="activeTab === 'info'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="bg-white rounded-xl shadow-sm overflow-hidden">
                    
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Informasi Profil</h2>
                                <p class="mt-1 text-sm text-gray-600">Perbarui informasi profil dan alamat email Anda</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 sm:p-8">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'password'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="bg-white rounded-xl shadow-sm overflow-hidden" 
                     style="display: none;">

                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Ubah Password</h2>
                                <p class="mt-1 text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'delete'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="bg-white rounded-xl shadow-sm overflow-hidden" 
                     style="display: none;">

                    <div class="bg-gradient-to-r from-red-50 to-pink-50 p-6 border-b border-red-100">
                        <div class="flex items-center">
                            <div class="bg-red-100 rounded-full p-3 mr-4">
                                <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Hapus Akun</h2>
                                <p class="mt-1 text-sm text-gray-600">Setelah akun Anda dihapus, semua data akan dihapus permanen. Harap berhati-hati.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="max-w-2xl">
                            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>