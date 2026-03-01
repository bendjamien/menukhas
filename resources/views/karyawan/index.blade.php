<x-app-layout>
    <div class="space-y-8" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        showDeleteModal: false,
        selectedKaryawan: { name: '', jabatan: '', no_hp: '', pin: '' },
        editUrl: '',
        deleteUrl: ''
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Manajemen Karyawan</h1>
                <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Kelola Data Staf Toko (Waiter, Chef, dll)</p>
            </div>
            <button @click="showAddModal = true" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white font-bold rounded-xl hover:bg-sky-700 transition-all text-sm shadow-lg shadow-sky-100 uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Karyawan
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Nama Karyawan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Jabatan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">PIN Absen</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">No. HP / WA</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($karyawans as $k)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $k->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-sky-50 text-sky-700 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $k->jabatan }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-sky-600">{{ $k->pin }}</td>
                            <td class="px-6 py-4 text-slate-500 font-medium italic">{{ $k->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button @click="
                                        selectedKaryawan = { name: '{{ $k->name }}', jabatan: '{{ $k->jabatan }}', no_hp: '{{ $k->no_hp }}', pin: '{{ $k->pin }}' };
                                        editUrl = '{{ route('karyawan.update', $k) }}';
                                        showEditModal = true;
                                    " class="p-2 text-sky-600 hover:bg-sky-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="
                                        selectedKaryawan.name = '{{ $k->name }}';
                                        deleteUrl = '{{ route('karyawan.destroy', $k) }}';
                                        showDeleteModal = true;
                                    " class="p-2 text-rose-400 hover:text-rose-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">{{ $karyawans->links() }}</div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-md rounded-[2rem] p-8 shadow-2xl" @click.away="showAddModal = false">
                <h2 class="text-2xl font-black text-slate-800 mb-6 uppercase tracking-tight">Tambah Karyawan</h2>
                <form action="{{ route('karyawan.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Posisi / Jabatan</label>
                        <input type="text" name="jabatan" required placeholder="Misal: Waiter, Chef, Helper..." class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">No. HP / WhatsApp (Wajib)</label>
                        <input type="text" name="no_hp" required placeholder="08xx..." class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">PIN Absensi (6 Angka)</label>
                        <input type="text" name="pin" required maxlength="6" pattern="[0-9]*" inputmode="numeric" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-mono font-bold text-sky-600 focus:ring-sky-500 text-center tracking-[1em]" placeholder="000000">
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showAddModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-sky-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-sky-100">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-md rounded-[2rem] p-8 shadow-2xl" @click.away="showEditModal = false">
                <h2 class="text-2xl font-black text-slate-800 mb-6 uppercase tracking-tight">Edit Karyawan</h2>
                <form :action="editUrl" method="POST" class="space-y-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" x-model="selectedKaryawan.name" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Posisi / Jabatan</label>
                        <input type="text" name="jabatan" x-model="selectedKaryawan.jabatan" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" x-model="selectedKaryawan.no_hp" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">PIN Absensi (6 Angka)</label>
                        <input type="text" name="pin" x-model="selectedKaryawan.pin" required maxlength="6" pattern="[0-9]*" inputmode="numeric" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-mono font-bold text-sky-600 focus:ring-sky-500 text-center tracking-[1em]">
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showEditModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button type="submit" class="flex-1 py-4 bg-sky-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-sky-100">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-sm rounded-[2rem] p-8 shadow-2xl text-center" @click.away="showDeleteModal = false">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Hapus Karyawan?</h2>
                <p class="text-slate-500 text-sm mb-8">Anda yakin ingin menghapus <span class="font-bold text-slate-800" x-text="selectedKaryawan.name"></span>? Data yang dihapus tidak dapat dikembalikan.</p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-rose-100">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>