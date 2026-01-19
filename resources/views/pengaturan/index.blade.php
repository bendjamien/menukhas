<x-app-layout>
    <div class="space-y-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Aplikasi</h1>
        </div>

        <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="space-y-6">
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Pajak (PPN)</h2>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tarif PPN (%)</label>
                            <div class="mt-1 flex items-center gap-2">
                                <input type="number" name="ppn_tax_rate" value="{{ old('ppn_tax_rate', $settings['ppn_tax_rate']) }}" step="0.01" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500" required>
                                <span class="text-gray-600 font-medium">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Loyalty Point Member</h2>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Min. Transaksi Dapat Poin</label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="loyalty_min_transaksi" value="{{ old('loyalty_min_transaksi', $settings['loyalty_min_transaksi'] ?? '50000') }}" class="block w-full border-gray-300 rounded-r-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nominal per 1 Poin</label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="loyalty_nominal_per_poin" value="{{ old('loyalty_nominal_per_poin', $settings['loyalty_nominal_per_poin'] ?? '10000') }}" class="block w-full border-gray-300 rounded-r-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Contoh: Tiap belanja 10.000 dapat 1 poin.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nilai Tukar 1 Poin</label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Rp</span>
                                    <input type="number" name="loyalty_nilai_rupiah_per_poin" value="{{ old('loyalty_nilai_rupiah_per_poin', $settings['loyalty_nilai_rupiah_per_poin'] ?? '500') }}" class="block w-full border-gray-300 rounded-r-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Nilai diskon saat poin ditukarkan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Jadwal Kerja</h2>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam Masuk</label>
                                <input type="time" name="jam_masuk_kantor" value="{{ old('jam_masuk_kantor', $settings['jam_masuk_kantor'] ?? '08:00') }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Toleransi</label>
                                <div class="flex items-center">
                                    <input type="number" name="toleransi_telat" value="{{ old('toleransi_telat', $settings['toleransi_telat'] ?? '0') }}" class="block w-full border-gray-300 rounded-l-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                    <span class="inline-flex items-center px-3 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Min</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam Pulang</label>
                                <input type="time" name="jam_pulang_kantor" value="{{ old('jam_pulang_kantor', $settings['jam_pulang_kantor'] ?? '17:00') }}" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                            </div>
                        </div>
                    </div>

                    <!-- PENGATURAN INVENTARIS -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-lg text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Inventaris</h2>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Batas Stok Menipis</label>
                            <div class="flex items-center">
                                <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $settings['stok_minimum'] ?? '5') }}" class="block w-full border-gray-300 rounded-l-lg shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                <span class="inline-flex items-center px-3 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">Unit</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Produk dengan stok di bawah angka ini akan ditandai "Menipis".</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-full">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-sky-100 rounded-lg text-sky-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-800">Identitas Toko</h2>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Perusahaan</label>
                                <div class="flex items-center space-x-4">
                                    <div class="shrink-0">
                                        @if(!empty($settings['company_logo']))
                                            <img class="h-16 w-16 object-contain rounded-md border border-gray-300 bg-white" 
                                                 src="{{ asset('storage/' . $settings['company_logo']) }}" 
                                                 alt="Logo Toko">
                                        @else
                                            <div class="h-16 w-16 rounded-md bg-gray-200 flex items-center justify-center text-gray-400">
                                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="company_logo" accept="image/*" 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500">Nama Bisnis</label>
                                <input type="text" name="company_name" value="{{ $settings['company_name'] }}" class="mt-1 block w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-sky-500 focus:border-sky-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Alamat Lengkap</label>
                                <textarea name="company_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-sky-500 focus:border-sky-500">{{ $settings['company_address'] }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Telepon / WA</label>
                                    <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}" class="mt-1 block w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Email Bisnis</label>
                                    <input type="email" name="company_email" value="{{ $settings['company_email'] }}" class="mt-1 block w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-sky-500 focus:border-sky-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition transform hover:scale-105">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>

        <hr class="border-gray-200">

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-green-100 rounded-lg text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Manajemen Voucher Diskon</h2>
            </div>

            <form action="{{ route('vouchers.store') }}" method="POST" class="mb-8 bg-gray-50 p-5 rounded-xl border border-gray-200">
                @csrf
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Voucher</label>
                        <input type="text" name="kode" placeholder="CONTOH: HEMAT10" class="block w-full rounded-lg border-gray-300 shadow-sm uppercase focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipe Potongan</label>
                        <select name="tipe" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="nominal">Potongan Harga (Rp)</option>
                            <option value="persen">Potongan Persen (%)</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nilai Potongan</label>
                        <input type="number" name="nilai" placeholder="Cth: 10000 atau 10" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500" required>
                    </div>
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 font-bold shadow-md transition">
                            + Tambah
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Diskon</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($vouchers as $voucher)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-sky-600 text-lg">{{ $voucher->kode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                                {{ $voucher->tipe == 'nominal' ? 'Rp '.number_format($voucher->nilai, 0, ',', '.') : $voucher->nilai . '%' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('vouchers.toggle', $voucher->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-3 py-1 text-xs font-bold rounded-full transition-colors {{ $voucher->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                        {{ $voucher->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button type="button" 
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'confirm-voucher-deletion-{{ $voucher->id }}')"
                                        class="text-red-500 hover:text-red-700 font-medium text-sm bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada voucher yang dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach($vouchers as $voucher)
        <x-modal :name="'confirm-voucher-deletion-'.$voucher->id" focusable>
            <form method="post" action="{{ route('vouchers.destroy', $voucher->id) }}" class="p-6">
                @csrf
                @method('delete')
                <h2 class="text-lg font-bold text-gray-900">Hapus Voucher ini?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Voucher <strong>{{ $voucher->kode }}</strong> akan dihapus permanen.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-danger-button>Ya, Hapus Voucher</x-danger-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>