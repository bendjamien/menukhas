<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Data Pelanggan</h1>
            
            <a href="{{ route('pelanggan.create') }}" 
               class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                + Tambah Pelanggan
            </a>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md">
            <form action="{{ route('pelanggan.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            Cari (Nama, No. HP, atau Email)
                        </label>
                        <input type="search" name="search" id="search" value="{{ $search ?? '' }}" 
                               placeholder="Ketik pencarian..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" 
                                class="w-full py-2 px-4 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition h-fit">
                            Cari
                        </button>
                        <a href="{{ route('pelanggan.index') }}" 
                           class="w-full text-center py-2 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition h-fit">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>


        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poin Member</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level Member</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($pelanggans as $pelanggan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $pelanggan->nama }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $pelanggan->no_hp }}</div>
                                    <div class="text-sm text-gray-500">{{ $pelanggan->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ Str::limit($pelanggan->alamat, 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ number_format($pelanggan->poin) }} Pts
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($pelanggan->member_level == 'Gold')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            👑 Gold
                                        </span>
                                    @elseif($pelanggan->member_level == 'Silver')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                            🥈 Silver
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                            🥉 Member
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    
                                    <a href="{{ route('pelanggan.show', $pelanggan) }}" class="text-sky-600 hover:text-sky-800 px-2">Detail</a>
                                    
                                    @if(Auth::user()->role == 'admin')
                                        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="text-yellow-600 hover:text-yellow-800 px-2">Edit</a>
                                        
                                        <button type="button" 
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-pelanggan-deletion-{{ $pelanggan->id }}')"
                                                class="text-red-600 hover:text-red-800 px-2">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data pelanggan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pelanggans->hasPages())
    <div class="p-4 ...">
        {{ $pelanggans->links() }}
    </div>
@endif
        </div>
    </div>

    @foreach ($pelanggans as $pelanggan)
        @if(Auth::user()->role == 'admin')
            <x-modal :name="'confirm-pelanggan-deletion-'.$pelanggan->id" focusable>
                <form method="post" action="{{ route('pelanggan.destroy', $pelanggan) }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-medium text-gray-900">
                        Apakah Anda yakin ingin menghapus data pelanggan ini?
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Anda akan menghapus: <strong>{{ $pelanggan->nama }}</strong>. <br>
                        Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Batal') }}
                        </x-secondary-button>

                        <x-danger-button class="ms-3">
                            {{ __('Ya, Hapus Pelanggan') }}
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endforeach
</x-app-layout>