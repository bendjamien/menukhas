<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $kategori->nama }}</h1>
                <p class="text-sm text-gray-500">Detail Kategori</p>
            </div>
            <a href="{{ route('kategori.index') }}" 
               class="mt-4 sm:mt-0 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-700">Deskripsi</h2>
            <p class="text-base text-gray-800 whitespace-pre-line">{{ $kategori->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
        </div>

        <div class="border-t pt-6 mt-8 flex justify-end">
            <a href="{{ route('kategori.edit', $kategori) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-200">
                Edit Kategori Ini
            </a>
        </div>

    </div>
</x-app-layout>