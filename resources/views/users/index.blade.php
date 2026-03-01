<x-app-layout>
    {{-- Seluruh konten dibungkus dalam satu div x-data agar scope variabel menjangkau hingga ke Modal di bawah --}}
    <div class="space-y-6" x-data="{ 
        showDeleteModal: false, 
        deleteUrl: '', 
        itemName: '',
        
        // State untuk Lihat PIN
        showViewPinConfirm: false,
        showPinResultModal: false,
        adminPassword: '',
        selectedUserId: null,
        revealedPin: '',
        isLoadingPin: false,

        async revealPin() {
            if(!this.adminPassword) {
                Toastify({ text: 'Masukkan password admin', duration: 2000, style: { background: '#f59e0b' } }).showToast();
                return;
            }
            this.isLoadingPin = true;
            try {
                let res = await fetch(`{{ url('users') }}/${this.selectedUserId}/view-pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ admin_password: this.adminPassword })
                });
                
                let data = await res.json();
                
                if(data.status === 'success') {
                    this.revealedPin = data.pin;
                    this.showViewPinConfirm = false;
                    this.showPinResultModal = true;
                    this.adminPassword = '';
                } else {
                    Toastify({ text: data.message, duration: 3000, style: { background: '#ef4444' } }).showToast();
                }
            } catch (e) {
                Toastify({ text: 'Gagal menghubungi server', duration: 3000, style: { background: '#ef4444' } }).showToast();
            } finally {
                this.isLoadingPin = false;
            }
        }
    }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight italic uppercase">Manajemen User</h1>
                <p class="text-sm text-gray-500 mt-1 font-bold uppercase tracking-widest">Kelola akun admin dan kasir toko</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-sky-100 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Tambah User
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">User</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Role & Kontak</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">PIN Keamanan</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600 font-bold border border-sky-200 uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $user->name }}</div>
                                            <div class="text-[10px] font-mono text-gray-400">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg border {{ $user->role == 'admin' ? 'bg-purple-50 text-purple-700 border-purple-100' : ($user->role == 'owner' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-blue-50 text-blue-700 border-blue-100') }}">
                                        {{ $user->role }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $user->status ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $user->status ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->request_new_pin)
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-[10px] font-black text-rose-600 uppercase animate-pulse tracking-tighter">🚨 Minta PIN Baru</span>
                                            <form action="{{ route('users.approve_pin', $user) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-[9px] font-black py-1.5 px-4 rounded-lg uppercase tracking-widest shadow-lg shadow-rose-100 transition-all active:scale-95">
                                                    Approve PIN
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <button @click="selectedUserId = {{ $user->id }}; itemName = '{{ $user->name }}'; showViewPinConfirm = true" class="text-sky-600 hover:text-sky-800 font-bold text-xs uppercase tracking-tighter flex items-center gap-1 mx-auto bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-100 transition-all active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c3.477 0 6.517 1.735 8.307 4.387a1.1 1.1 0 010 1.226C18.517 17.265 15.477 19 12 19c-4.477 0-7.523-2.943-9.542-7z"></path></svg>
                                            Lihat PIN
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('users.edit', $user->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        @if($user->role !== 'admin')
                                        <button @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteUrl = '{{ route('users.destroy', $user->id) }}'; itemName = '{{ $user->name }}'" 
                                                class="p-2 text-rose-400 hover:text-rose-600 transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Konfirmasi Password Admin -->
        <div x-show="showViewPinConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl" @click.away="showViewPinConfirm = false">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 uppercase">Verifikasi Admin</h3>
                    <p class="text-sm text-gray-500 mt-1">Masukkan password Anda untuk melihat PIN <span class="font-bold text-gray-800" x-text="itemName"></span></p>
                </div>
                
                <div class="space-y-4">
                    <input type="password" x-model="adminPassword" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl py-4 px-5 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all text-center text-lg" placeholder="Password Anda" @keydown.enter="revealPin()">
                    
                    <div class="flex gap-3">
                        <button @click="showViewPinConfirm = false; adminPassword = ''" class="flex-1 py-4 bg-gray-100 text-gray-500 font-black rounded-2xl uppercase tracking-widest text-[10px]">Batal</button>
                        <button @click="revealPin()" :disabled="isLoadingPin" class="flex-1 py-4 bg-sky-600 text-white font-black rounded-2xl uppercase tracking-widest text-[10px] shadow-lg shadow-sky-100 disabled:opacity-50">
                            <span x-show="!isLoadingPin">Verifikasi</span>
                            <span x-show="isLoadingPin">Proses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tampilkan PIN Hasil -->
        <div x-show="showPinResultModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl text-center" @click.away="showPinResultModal = false">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">PIN Keamanan User</p>
                <h3 class="text-sm font-bold text-gray-600 mb-6" x-text="itemName"></h3>
                
                <div class="bg-sky-50 rounded-3xl py-8 mb-8 border border-sky-100 shadow-inner text-center">
                    <span class="text-5xl font-black text-sky-600 tracking-[0.2em] font-mono inline-block w-full" x-text="revealedPin"></span>
                </div>

                <button @click="showPinResultModal = false; revealedPin = ''" class="w-full py-4 bg-gray-900 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-xl transition-all active:scale-95">Tutup</button>
            </div>
        </div>

        <!-- Delete Confirmation Modal (Standar) -->
        <x-modal name="confirm-delete-modal" focusable maxWidth="sm">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Hapus User?</h2>
                <p class="text-slate-500 text-sm mb-8">Anda yakin ingin menghapus akun <span class="font-bold text-slate-800" x-text="itemName"></span>? Akun ini tidak akan bisa login lagi ke sistem.</p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf @method('DELETE')
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-4 bg-slate-100 text-slate-500 font-black rounded-xl uppercase tracking-widest text-[10px]">Batal</button>
                    <button type="submit" class="flex-1 py-4 bg-rose-600 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-rose-100">Ya, Hapus</button>
                </form>
            </div>
        </x-modal>
    </div> {{-- Penutup x-data --}}
</x-app-layout>