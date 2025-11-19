<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Data Kategori Produk</h1>
            <a href="{{ route('kategori.create') }}" 
               class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                + Tambah Kategori
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($kategoris as $kategori)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kategori->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $kategori->deskripsi ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <a href="{{ route('kategori.show', $kategori) }}" class="text-sky-600 hover:text-sky-800 px-2">Detail</a>
                                    <a href="{{ route('kategori.edit', $kategori) }}" class="text-yellow-600 hover:text-yellow-800 px-2">Edit</a>
                                    
                                    <button type="button" 
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-kategori-deletion-{{ $kategori->id }}')"
                                            class="text-red-600 hover:text-red-800 px-2">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kategoris->hasPages())
                <div class="p-4 bg-gray-50 border-t border-gray-200">{{ $kategoris->links() }}</div>
            @endif
        </div>
    </div>

    @foreach ($kategoris as $kategori)
        <x-modal :name="'confirm-kategori-deletion-'.$kategori->id" focusable>
            <form method="post" action="{{ route('kategori.destroy', $kategori) }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    Apakah Anda yakin ingin menghapus kategori ini?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Anda akan menghapus: <strong>{{ $kategori->nama }}</strong>. <br>
                    Menghapus kategori juga akan menghapus semua produk di dalamnya. Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Ya, Hapus Kategori') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>