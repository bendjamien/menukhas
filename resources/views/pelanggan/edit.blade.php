<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto"> <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Data Pelanggan</h1>
            <a href="{{ route('pelanggan.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('pelanggan.update', $pelanggan) }}" method="POST">
            @csrf
            @method('PUT') <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $pelanggan->nama) }}" 
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                           required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $pelanggan->email) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label for="member_level" class="block text-sm font-medium text-gray-700">Member Level</label>
                    <input type="text" name="member_level" id="member_level" value="{{ old('member_level', $pelanggan->member_level) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" 
                              class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                </div>

                <div>
                    <label for="poin" class="block text-sm font-medium text-gray-700">Poin</label>
                    <input type="number" name="poin" id="poin" value="{{ old('poin', $pelanggan->poin) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" disabled>
                </div>
            
            </div> <div class="flex justify-end pt-6 mt-6 border-t">
                <button type="submit" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>