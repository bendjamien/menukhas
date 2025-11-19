<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Kategori</h1>
            <a href="{{ route('kategori.index') }}" class="text-sm text-gray-600 hover:text-sky-500">&larr; Kembali ke Daftar</a>
        </div>

        <form action="{{ route('kategori.update', $kategori) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $kategori->nama) }}" 
                       class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                       required>
            </div>
            
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" 
                          class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>