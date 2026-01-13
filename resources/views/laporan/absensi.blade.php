<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Laporan Absensi Pegawai</h1>
            </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jam Pulang</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Keterlambatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($absensis as $absen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $absen->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::parse($absen->tanggal)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-mono">{{ $absen->jam_masuk ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-mono">{{ $absen->jam_pulang ?? '-' }}</td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($absen->status == 'Telat')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Telat
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Tepat Waktu
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($absen->keterlambatan > 0)
                                <span class="text-red-600 font-bold">+ {{ $absen->keterlambatan }} Menit</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4 border-t">
                {{ $absensis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>