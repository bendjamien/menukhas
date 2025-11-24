<x-app-layout>
    <style>
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
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun (User)</h1>
            
            <a href="{{ route('users.create') }}" 
               class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                + Tambah Akun Baru
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama / Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak (Email)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Aktif</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->username }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->role == 'admin' ? 'bg-sky-100 text-sky-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(Auth::user()->id != $user->id)
                                        <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
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
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500 cursor-not-allowed">
                                            Aktif (Anda)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800 px-3 py-1 rounded hover:bg-yellow-50 transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada data user.
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
</x-app-layout>