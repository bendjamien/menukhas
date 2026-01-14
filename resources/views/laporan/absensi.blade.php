<x-app-layout>
    <div x-data="{ 
            showDetailModal: false, 
            isLoading: false,
            detailUser: null,
            detailData: [],
            detailRole: '',
            detailPeriode: '',
            settings: {},
            openDetail(userId) {
                this.isLoading = true;
                this.showDetailModal = true;
                
                // Fetch Data via AJAX
                fetch(`{{ url('laporan/absensi') }}/${userId}?bulan={{ $bulan }}&tahun={{ $tahun }}`)
                    .then(response => response.json())
                    .then(data => {
                        this.detailUser = data.user;
                        this.detailRole = data.role;
                        this.detailData = data.data;
                        this.detailPeriode = data.periode;
                        this.settings = data.settings;
                        this.isLoading = false;
                        this.currentUserId = userId; // Store ID for print link
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.isLoading = false;
                    });
            },
            currentUserId: null
         }">

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Absensi Pegawai</h1>
                <p class="text-sm text-gray-500">Rekapitulasi kehadiran kasir & staff</p>
            </div>

            <!-- Filter Bulan & Tahun -->
            <form action="{{ route('laporan.absensi') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                <select name="bulan" class="border-none text-sm font-semibold text-gray-700 focus:ring-0 cursor-pointer bg-transparent">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <div class="w-px h-6 bg-gray-200"></div>
                <select name="tahun" class="border-none text-sm font-semibold text-gray-700 focus:ring-0 cursor-pointer bg-transparent">
                    @foreach(range(date('Y')-2, date('Y')) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-sky-500 text-white p-2 rounded-lg hover:bg-sky-600 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Hadir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Telat (Kali)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Menit Telat</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($laporan as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-lg">
                                            {{ substr($row['user']->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $row['user']->name }}</div>
                                        <div class="text-xs text-gray-500 capitalize">{{ $row['user']->role }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $row['total_hadir'] }} Hari
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row['total_telat'] > 0 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $row['total_telat'] }} Kali
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
                                {{ $row['total_menit_telat'] }} Menit
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button @click="openDetail({{ $row['user']->id }})" class="text-sky-600 hover:text-sky-900 font-bold text-sm bg-sky-50 px-4 py-2 rounded-lg hover:bg-sky-100 transition">
                                    Lihat Detail & Cetak
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                Belum ada data absensi pada bulan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL DETAIL (Redesigned) -->
        <div x-show="showDetailModal" style="display: none;" 
            class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true" @click="showDetailModal = false"></div>

                <!-- Spacer -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div class="inline-block w-full max-w-3xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl sm:align-middle">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between px-8 py-6 bg-white border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" x-text="detailUser"></h3>
                            <p class="mt-1 text-sm font-medium text-gray-500" x-text="detailPeriode"></p>
                        </div>
                        <button @click="showDetailModal = false" class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-8 py-6">
                        
                        <div x-show="isLoading" class="flex flex-col items-center justify-center py-12">
                            <div class="w-12 h-12 mb-4 border-4 rounded-full border-sky-200 animate-spin border-t-sky-600"></div>
                            <p class="text-sm font-medium text-gray-500">Memuat data absensi...</p>
                        </div>

                        <div x-show="!isLoading" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            
                            <!-- Summary Stats Cards -->
                            <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-3">
                                <div class="p-5 border border-blue-100 bg-blue-50/50 rounded-2xl">
                                    <div class="flex items-center mb-2 space-x-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-xs font-bold tracking-wider text-blue-600 uppercase">Jadwal Masuk</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-blue-900" x-text="settings.jam_masuk"></p>
                                </div>
                                <div class="p-5 border border-purple-100 bg-purple-50/50 rounded-2xl">
                                    <div class="flex items-center mb-2 space-x-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <span class="text-xs font-bold tracking-wider text-purple-600 uppercase">Jadwal Pulang</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-purple-900" x-text="settings.jam_pulang"></p>
                                </div>
                                <div class="p-5 border border-emerald-100 bg-emerald-50/50 rounded-2xl">
                                    <div class="flex items-center mb-2 space-x-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-xs font-bold tracking-wider text-emerald-600 uppercase">Kehadiran</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-emerald-900" x-text="detailData.length + ' Hari'"></p>
                                </div>
                            </div>

                            <!-- Modern Table -->
                            <div class="overflow-hidden border border-gray-200 rounded-xl">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                            <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">Masuk</th>
                                            <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">Pulang</th>
                                            <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-center text-gray-500 uppercase">Status</th>
                                            <th scope="col" class="px-6 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-if="detailData.length === 0">
                                            <tr><td colspan="5" class="py-10 text-center text-gray-500 italic">Tidak ada data absensi untuk periode ini.</td></tr>
                                        </template>

                                        <template x-for="item in detailData" :key="item.id">
                                            <tr class="transition-colors hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <span x-text="new Date(item.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })"></span>
                                                        <span class="text-xs font-normal text-gray-400 capitalize" x-text="new Date(item.tanggal).toLocaleDateString('id-ID', { weekday: 'long' })"></span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 font-mono text-sm text-center text-gray-600 whitespace-nowrap" x-text="item.waktu_masuk"></td>
                                                <td class="px-6 py-4 font-mono text-sm text-center text-gray-600 whitespace-nowrap" x-text="item.waktu_keluar || '--:--'"></td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                          :class="item.status === 'Telat' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10' : 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20'">
                                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full" :class="item.status === 'Telat' ? 'bg-red-500' : 'bg-green-500'"></span>
                                                        <span x-text="item.status"></span>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                                    <div x-show="item.status === 'Telat'" class="flex items-center text-red-600">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span class="font-semibold" x-text="'+ ' + item.keterlambatan + ' mnt'"></span>
                                                    </div>
                                                    <span x-show="item.status !== 'Telat'" class="text-gray-300 font-light italic">Tepat waktu</span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-xs text-gray-400 hidden sm:block">
                            * Data diperbarui real-time dari sistem absensi
                        </div>
                        <div class="flex items-center justify-end w-full sm:w-auto space-x-3">
                            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" @click="showDetailModal = false">
                                Tutup
                            </button>
                            <a :href="`{{ url('laporan/absensi') }}/${currentUserId}/print?bulan={{ $bulan }}&tahun={{ $tahun }}`" target="_blank" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors border border-transparent rounded-lg shadow-sm bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Cetak Laporan PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>