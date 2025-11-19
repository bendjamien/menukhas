<x-app-layout>
    <div class="bg-white p-8 rounded-lg shadow-md max-w-3xl mx-auto">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b pb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $pelanggan->nama }}</h1>
                <p class="text-sm text-gray-500">Detail Pelanggan</p>
            </div>
            <a href="{{ route('pelanggan.index') }}" 
               class="mt-4 sm:mt-0 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                &larr; Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            
            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Email</span>
                <span class="text-lg text-gray-800">{{ $pelanggan->email ?? '-' }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">No. HP</span>
                <span class="text-lg text-gray-800">{{ $pelanggan->no_hp ?? '-' }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Member Level</span>
                <span class="text-lg text-gray-800">{{ $pelanggan->member_level }}</span>
            </div>

            <div class="flex flex-col">
                <span class="text-xs font-medium text-gray-500 uppercase">Poin</span>
                <span class="text-lg text-gray-800 font-semibold text-sky-600">{{ $pelanggan->poin }}</span>
            </div>

            <div class="flex flex-col md:col-span-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Alamat</span>
                <p class="text-lg text-gray-800 whitespace-pre-line">{{ $pelanggan->alamat ?? '-' }}</p>
            </div>

        </div>

        <div class="border-t pt-6 mt-8 flex justify-end">
            <a href="{{ route('pelanggan.edit', $pelanggan) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-200">
                Edit Pelanggan Ini
            </a>
        </div>

    </div>
</x-app-layout>