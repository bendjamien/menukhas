<x-app-layout>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Aplikasi</h1>

        <form action="{{ route('pengaturan.update') }}" method="POST">
            @csrf
            
            <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Pengaturan Pajak (PPN)</h2>
                
                <div>
                    <label for="ppn_tax_rate" class="block text-sm font-medium text-gray-700">
                        Tarif PPN (Pajak Pertambahan Nilai)
                    </label>
                    <div class="mt-1 flex items-center gap-2">
                        <input type="number" name="ppn_tax_rate" id="ppn_tax_rate" 
                               value="{{ old('ppn_tax_rate', $settings['ppn_tax_rate']) }}" 
                               step="0.01"
                               class="block w-48 border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                               required>
                        <span class="text-lg font-medium text-gray-600">%</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Masukkan '11' untuk 11%. Masukkan '0' untuk menonaktifkan pajak.</p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Informasi Perusahaan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Bisnis</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500" 
                               required>
                    </div>
                    <div>
                        <label for="company_website" class="block text-sm font-medium text-gray-700">Website</label>
                        <input type="text" name="company_website" id="company_website" value="{{ old('company_website', $settings['company_website']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700">Email Kontak</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings['company_email']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700">Alamat</Slebel>
                        <input type="text" name="company_address" id="company_address" value="{{ old('company_address', $settings['company_address']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="company_tax_id" class="block text-sm font-medium text-gray-700">ID Pajak (NPWP)</label>
                        <input type="text" name="company_tax_id" id="company_tax_id" value="{{ old('company_tax_id', $settings['company_tax_id']) }}" 
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mt-6 flex justify-end">
                <button type="submit" 
                        class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                    Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>