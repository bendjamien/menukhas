<x-app-layout>
    <div x-data="{ activeTab: '{{ session('active_tab', 'umum') }}' }" class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">Pengaturan Sistem</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola konfigurasi aplikasi dan preferensi bisnis Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- LEFT COLUMN: NAVIGATION TABS -->
            <div class="lg:col-span-1 space-y-2 sticky top-24 self-start">
                
                <button @click="activeTab = 'umum'" 
                        :class="activeTab === 'umum' ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-200 group">
                    <div :class="activeTab === 'umum' ? 'bg-sky-200 text-sky-700' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200'" class="p-2 rounded-lg mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    Identitas Toko
                </button>

                <button @click="activeTab = 'keuangan'" 
                        :class="activeTab === 'keuangan' ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-200 group">
                    <div :class="activeTab === 'keuangan' ? 'bg-sky-200 text-sky-700' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200'" class="p-2 rounded-lg mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Keuangan & Stok
                </button>

                <button @click="activeTab = 'loyalty'" 
                        :class="activeTab === 'loyalty' ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-200 group">
                    <div :class="activeTab === 'loyalty' ? 'bg-sky-200 text-sky-700' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200'" class="p-2 rounded-lg mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    Loyalty Member
                </button>

                <button @click="activeTab = 'jadwal'" 
                        :class="activeTab === 'jadwal' ? 'bg-sky-50 text-sky-700 shadow-sm ring-1 ring-sky-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-200 group">
                    <div :class="activeTab === 'jadwal' ? 'bg-sky-200 text-sky-700' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200'" class="p-2 rounded-lg mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Jam Kerja
                </button>

                <div class="pt-4 mt-4 border-t border-gray-100"></div>

                <button @click="activeTab = 'voucher'" 
                        :class="activeTab === 'voucher' ? 'bg-green-50 text-green-700 shadow-sm ring-1 ring-green-200' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                        class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all duration-200 group">
                    <div :class="activeTab === 'voucher' ? 'bg-green-200 text-green-700' : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200'" class="p-2 rounded-lg mr-3 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    Voucher Diskon
                </button>

            </div>

            <!-- RIGHT COLUMN: CONTENT FORMS -->
            <div class="lg:col-span-3">
                
                <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="active_tab" x-model="activeTab">
                    
                    <!-- TAB 1: UMUM (IDENTITAS TOKO) -->
                    <div x-show="activeTab === 'umum'" x-transition:enter.duration.300ms class="space-y-6">
                        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                            <h2 class="text-xl font-black text-gray-800 mb-6">Identitas Toko</h2>
                            
                            <!-- Logo Upload -->
                            <div class="mb-8">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Logo Perusahaan</label>
                                <div class="flex items-center gap-6">
                                    <div class="shrink-0 relative group">
                                        @if(!empty($settings['company_logo']))
                                            <img class="h-24 w-24 object-contain rounded-2xl border-2 border-gray-100 bg-white shadow-sm" src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo">
                                        @else
                                            <div class="h-24 w-24 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-300 flex items-center justify-center text-slate-400">
                                                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="company_logo" accept="image/*" class="block w-full text-sm text-slate-500
                                          file:mr-4 file:py-2.5 file:px-4
                                          file:rounded-xl file:border-0
                                          file:text-sm file:font-bold
                                          file:bg-sky-50 file:text-sky-700
                                          hover:file:bg-sky-100
                                          transition-all cursor-pointer
                                        "/>
                                        <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG (Max 2MB). Disarankan rasio 1:1.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Bisnis</label>
                                    <input type="text" name="company_name" value="{{ $settings['company_name'] }}" class="w-full border-gray-200 rounded-xl focus:ring-sky-500 focus:border-sky-500 font-bold text-gray-800">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alamat Lengkap</label>
                                    <textarea name="company_address" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-sky-500 focus:border-sky-500">{{ $settings['company_address'] }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor Telepon / WA</label>
                                    <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}" class="w-full border-gray-200 rounded-xl focus:ring-sky-500 focus:border-sky-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Bisnis</label>
                                    <input type="email" name="company_email" value="{{ $settings['company_email'] }}" class="w-full border-gray-200 rounded-xl focus:ring-sky-500 focus:border-sky-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: KEUANGAN -->
                    <div x-show="activeTab === 'keuangan'" x-transition:enter.duration.300ms class="space-y-6">
                        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                            <h2 class="text-xl font-black text-gray-800 mb-6">Pengaturan Keuangan & Stok</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tarif Pajak (PPN)</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <input type="number" name="ppn_tax_rate" value="{{ old('ppn_tax_rate', $settings['ppn_tax_rate']) }}" step="0.01" class="block w-full border-gray-200 rounded-xl pl-4 pr-12 focus:ring-sky-500 focus:border-sky-500 py-3 font-bold text-lg">
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">%</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Persentase pajak yang ditambahkan ke total transaksi.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Batas Stok Menipis</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $settings['stok_minimum'] ?? '5') }}" class="block w-full border-gray-200 rounded-xl pl-4 pr-16 focus:ring-sky-500 focus:border-sky-500 py-3 font-bold text-lg">
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">Unit</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2">Notifikasi akan muncul jika stok produk di bawah angka ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: LOYALTY -->
                    <div x-show="activeTab === 'loyalty'" x-transition:enter.duration.300ms class="space-y-6">
                        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-black text-gray-800">Program Loyalty Member</h2>
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">Membership</span>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-purple-50 p-4 rounded-xl border border-purple-100 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm text-purple-800 leading-relaxed">
                                        Sistem poin otomatis menghitung poin pelanggan berdasarkan total belanja. Poin dapat ditukarkan sebagai diskon pada transaksi berikutnya.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Minimal Belanja (Trigger Poin)</label>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-4 py-3 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-500 font-bold text-sm">Rp</span>
                                            <input type="number" name="loyalty_min_transaksi" value="{{ old('loyalty_min_transaksi', $settings['loyalty_min_transaksi'] ?? '50000') }}" class="block w-full border-gray-200 rounded-r-xl shadow-sm focus:ring-purple-500 focus:border-purple-500 font-bold">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal per 1 Poin</label>
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-4 py-3 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-500 font-bold text-sm">Rp</span>
                                                <input type="number" name="loyalty_nominal_per_poin" value="{{ old('loyalty_nominal_per_poin', $settings['loyalty_nominal_per_poin'] ?? '10000') }}" class="block w-full border-gray-200 rounded-r-xl shadow-sm focus:ring-purple-500 focus:border-purple-500 font-bold">
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Contoh: Belanja 10.000 dapat 1 Poin.</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nilai Tukar 1 Poin</label>
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-4 py-3 rounded-l-xl border border-r-0 border-gray-200 bg-gray-50 text-gray-500 font-bold text-sm">Rp</span>
                                                <input type="number" name="loyalty_nilai_rupiah_per_poin" value="{{ old('loyalty_nilai_rupiah_per_poin', $settings['loyalty_nilai_rupiah_per_poin'] ?? '500') }}" class="block w-full border-gray-200 rounded-r-xl shadow-sm focus:ring-purple-500 focus:border-purple-500 font-bold">
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Nilai rupiah saat poin diredeem.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: JAM KERJA -->
                    <div x-show="activeTab === 'jadwal'" x-transition:enter.duration.300ms class="space-y-6">
                        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                            <h2 class="text-xl font-black text-gray-800 mb-6">Jam Operasional Kantor</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Masuk</label>
                                    <input type="time" name="jam_masuk_kantor" value="{{ old('jam_masuk_kantor', $settings['jam_masuk_kantor'] ?? '08:00') }}" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-sky-500 focus:border-sky-500 py-3 text-center font-bold text-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Toleransi Telat (Menit)</label>
                                    <input type="number" name="toleransi_telat" value="{{ old('toleransi_telat', $settings['toleransi_telat'] ?? '0') }}" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-sky-500 focus:border-sky-500 py-3 text-center font-bold text-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jam Pulang</label>
                                    <input type="time" name="jam_pulang_kantor" value="{{ old('jam_pulang_kantor', $settings['jam_pulang_kantor'] ?? '17:00') }}" class="block w-full border-gray-200 rounded-xl shadow-sm focus:ring-sky-500 focus:border-sky-500 py-3 text-center font-bold text-lg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SAVE BUTTON (Floating Bottom) -->
                    <div x-show="activeTab !== 'voucher'" class="mt-8 flex justify-end">
                        <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-sky-300 transition-all transform hover:scale-105 hover:-translate-y-1 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

                <!-- TAB VOUCHER (SEPARATE LOGIC) -->
                <div x-show="activeTab === 'voucher'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-100 border border-gray-100">
                        <h2 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            <span class="bg-green-100 p-2 rounded-lg text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg></span>
                            Manajemen Voucher
                        </h2>

                        <!-- Form Tambah -->
                        <form action="{{ route('vouchers.store') }}" method="POST" class="mb-8 bg-green-50/50 p-6 rounded-2xl border border-green-100">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <div class="col-span-1">
                                    <label class="block text-xs font-bold text-green-800 uppercase mb-1">Kode Voucher</label>
                                    <input type="text" name="kode" placeholder="HEMAT10" class="block w-full rounded-xl border-green-200 focus:ring-green-500 focus:border-green-500 font-mono font-bold uppercase" required>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-xs font-bold text-green-800 uppercase mb-1">Tipe</label>
                                    <select name="tipe" class="block w-full rounded-xl border-green-200 focus:ring-green-500 focus:border-green-500 font-medium">
                                        <option value="nominal">Rupiah (Rp)</option>
                                        <option value="persen">Persen (%)</option>
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-xs font-bold text-green-800 uppercase mb-1">Nilai</label>
                                    <input type="number" name="nilai" placeholder="10000" class="block w-full rounded-xl border-green-200 focus:ring-green-500 focus:border-green-500 font-bold" required>
                                </div>
                                <div class="col-span-1">
                                    <button type="submit" class="w-full bg-green-600 text-white px-6 py-2.5 rounded-xl hover:bg-green-700 font-bold shadow-md transition-all transform active:scale-95">
                                        + Buat Baru
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabel -->
                        <div class="overflow-hidden rounded-2xl border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Potongan</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($vouchers as $voucher)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-mono font-black text-gray-700 bg-gray-100 px-2 py-1 rounded text-sm">{{ $voucher->kode }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                            {{ $voucher->tipe == 'nominal' ? 'Rp '.number_format($voucher->nilai, 0, ',', '.') : $voucher->nilai . '%' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('vouchers.toggle', $voucher->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none {{ $voucher->is_active ? 'bg-green-500' : 'bg-gray-200' }}">
                                                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $voucher->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button type="button" 
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-voucher-deletion-{{ $voucher->id }}')"
                                                    class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                            Belum ada voucher. Buat satu sekarang!
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @foreach($vouchers as $voucher)
        <x-modal :name="'confirm-voucher-deletion-'.$voucher->id" focusable>
            <form method="post" action="{{ route('vouchers.destroy', $voucher->id) }}" class="p-6 text-center">
                @csrf
                @method('delete')
                <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus Voucher?</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Kode <strong>{{ $voucher->kode }}</strong> akan dihapus permanen dan tidak bisa digunakan lagi.
                </p>
                <div class="flex justify-center gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>