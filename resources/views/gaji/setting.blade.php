<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Atur Gaji Pokok</h1>
            <p class="text-slate-500 text-sm mt-1 uppercase tracking-widest font-bold">Tentukan Nominal Gaji Bulanan Karyawan</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                    <form action="{{ route('gaji.setting.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Karyawan</label>
                            <select name="user_id" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-sky-500">
                                <option value="">-- Pilih --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Gaji Pokok (Rp)</label>
                            <input type="number" name="gaji_pokok" required class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-bold text-slate-700" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Bank (Opsional)</label>
                            <input type="text" name="bank" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-medium text-slate-700" placeholder="BCA / Mandiri / BRI">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">No. Rekening (Opsional)</label>
                            <input type="text" name="nomor_rekening" class="w-full bg-slate-50 border-slate-200 rounded-xl py-3 px-4 font-medium text-slate-700" placeholder="000111222">
                        </div>
                        <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-4 rounded-xl shadow-lg shadow-sky-100 uppercase tracking-widest transition-all">Simpan Aturan</button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Karyawan</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Gaji Pokok</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Info Rekening</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($settings as $s)
                                <tr>
                                    <td class="px-6 py-4 font-bold text-slate-700">{{ $s->user->name }}</td>
                                    <td class="px-6 py-4 font-black text-sky-600">Rp {{ number_format($s->gaji_pokok, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                                        {{ $s->bank ?? '-' }} : {{ $s->nomor_rekening ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>