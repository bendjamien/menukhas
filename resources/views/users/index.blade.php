<x-app-layout>
    <style>
        /* CSS Khusus Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10B981; 
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #10B981; 
        }
        .toggle-checkbox:not(:checked) + .toggle-label {
            background-color: #EF4444; 
        }
    </style>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun (User)</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data pegawai, admin, dan owner.</p>
            </div>
            
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center bg-sky-600 hover:bg-sky-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Akun Baru
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User Info</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->avatar)
                                                <img class="h-10 w-10 rounded-full object-cover border border-gray-200" 
                                                     src="{{ asset('storage/' . $user->avatar) }}" 
                                                     alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-sm border border-sky-200">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">@ {{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $badgeColor = match($user->role) {
                                            'admin' => 'bg-blue-100 text-blue-800',
                                            'owner' => 'bg-purple-100 text-purple-800',
                                            'kasir' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(Auth::user()->id != $user->id)
                                        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                                                <input type="checkbox" name="toggle" id="toggle-{{ $user->id }}" 
                                                       class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 ease-in-out"
                                                       onchange="this.form.submit()"
                                                       {{ $user->status == 1 ? 'checked' : '' }}
                                                       style="top: 0; {{ $user->status == 1 ? 'right: 0; border-color: #10B981;' : 'left: 0; border-color: #EF4444;' }}"/>
                                                
                                                <label for="toggle-{{ $user->id }}" 
                                                       class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300 ease-in-out"
                                                       style="{{ $user->status == 1 ? 'background-color: #10B981;' : 'background-color: #EF4444;' }}">
                                                </label>
                                            </div>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Aktif (Anda)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        
                                        @if($user->status)
                                            @if($user->request_new_pin)
                                                <form action="{{ route('users.approve_pin', $user) }}" method="POST" class="inline-block form-approve-pin">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" 
                                                       class="btn-approve-pin text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-2 rounded-lg transition animate-pulse ring-1 ring-green-200" 
                                                       title="Setujui Perubahan PIN"
                                                       data-name="{{ $user->name }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-block text-gray-400 bg-gray-50 p-2 rounded-lg cursor-not-allowed" title="PIN Aman (Tidak ada permintaan)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        @endif

                                        <button type="button"
                                                onclick="managePin({{ $user->id }}, '{{ $user->name }}')"
                                                class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                                title="Lihat / Reset PIN">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </button>

                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition" 
                                           title="Edit User">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p class="text-base">Belum ada data user yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function managePin(userId, userName) {
            Swal.fire({
                title: `Kelola PIN: ${userName}`,
                text: "Apa yang ingin Anda lakukan?",
                icon: 'info',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Lihat PIN',
                denyButtonText: 'Reset PIN',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#3B82F6', // Blue
                denyButtonColor: '#EF4444', // Red
                cancelButtonColor: '#6B7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    // --- LIHAT PIN ---
                    Swal.fire({
                        title: 'Verifikasi Admin',
                        input: 'password',
                        inputLabel: 'Masukkan Password Admin Anda',
                        inputPlaceholder: 'Password...',
                        showCancelButton: true,
                        confirmButtonText: 'Buka PIN',
                        showLoaderOnConfirm: true,
                        preConfirm: (password) => {
                            return fetch(`{{ url('users') }}/${userId}/view-pin`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ admin_password: password })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText)
                                }
                                return response.json()
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`)
                            })
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if(result.value.status === 'success') {
                                Swal.fire({
                                    title: `PIN ${userName}`,
                                    text: result.value.pin,
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire('Gagal', result.value.message, 'error');
                            }
                        }
                    })

                } else if (result.isDenied) {
                    // --- RESET PIN ---
                    Swal.fire({
                        title: 'Reset PIN',
                        html:
                            '<input id="swal-admin-pass" class="swal2-input" placeholder="Password Admin Anda" type="password">' +
                            '<input id="swal-new-pin" class="swal2-input" placeholder="PIN Baru (6 Angka)" type="number" maxlength="6">',
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Reset Sekarang',
                        confirmButtonColor: '#EF4444',
                        preConfirm: () => {
                            const password = document.getElementById('swal-admin-pass').value;
                            const newPin = document.getElementById('swal-new-pin').value;
                            
                            if (!password || !newPin) {
                                Swal.showValidationMessage('Harap isi kedua kolom!');
                                return false;
                            }
                            if (newPin.length !== 6) {
                                Swal.showValidationMessage('PIN harus 6 digit!');
                                return false;
                            }

                            return fetch(`{{ url('users') }}/${userId}/reset-pin`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ 
                                    admin_password: password,
                                    new_pin: newPin
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if(data.status !== 'success') {
                                    throw new Error(data.message || 'Gagal reset');
                                }
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`${error.message}`)
                            })
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Berhasil!', 'PIN telah direset.', 'success');
                        }
                    })
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn-approve-pin');
            
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.form-approve-pin');
                    const userName = this.dataset.name;
                    
                    Swal.fire({
                        title: 'Setujui PIN Baru?',
                        text: `Anda akan menyetujui perubahan PIN untuk user "${userName}".`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981', // Emerald 500
                        cancelButtonColor: '#6B7280', // Gray 500
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-4 py-2 rounded-lg',
                            cancelButton: 'px-4 py-2 rounded-lg'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>