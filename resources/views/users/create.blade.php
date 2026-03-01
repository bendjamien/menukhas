<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Akun Baru</h1>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                           required>
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">User</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                           required>
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Untuk Login)</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role (Hak Akses)</label>
                    <select id="role" name="role" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                        <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Akun</label>
                    <div class="mt-2 space-y-1">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="1" class="form-radio text-sky-600" checked>
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input type="radio" name="status" value="0" class="form-radio text-red-600">
                            <span class="ml-2 text-sm text-gray-700">Non-Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- JADWAL SHIFT KERJA -->
            <div class="border-t pt-4">
                <p class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Jadwal Shift Kerja
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-sky-50 p-4 rounded-xl border border-sky-100">
                    <div>
                        <label for="jam_masuk" class="block text-xs font-black text-sky-700 uppercase tracking-widest mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="jam_masuk" value="{{ old('jam_masuk', '08:00') }}" 
                               class="w-full border-gray-200 rounded-lg focus:ring-sky-500 focus:border-sky-500 text-sm font-bold">
                    </div>
                    <div>
                        <label for="jam_pulang" class="block text-xs font-black text-sky-700 uppercase tracking-widest mb-1">Jam Pulang (Tutup Kasir)</label>
                        <input type="time" name="jam_pulang" id="jam_pulang" value="{{ old('jam_pulang', '17:00') }}" 
                               class="w-full border-gray-200 rounded-lg focus:ring-sky-500 focus:border-sky-500 text-sm font-bold">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                           required>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500"
                           required>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
