<x-app-layout>
    <div 
        class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md"
        x-data="{ 
            subtotal: {{ $subtotal }}, 
            taxRate: {{ $taxRate }}, 
            discountInput: 0, 
            nominalBayar: {{ $subtotal }}, 
            metodeBayar: 'Tunai',

            get diskonAmount() {
                let disc = parseFloat(this.discountInput);
                if (isNaN(disc) || disc < 0) return 0;
                return (disc > this.subtotal) ? this.subtotal : disc;
            },
            get totalAfterDiscount() {
                return this.subtotal - this.diskonAmount;
            },
            get taxAmount() {
                return this.totalAfterDiscount * this.taxRate;
            },
            get grandTotal() {
                return this.totalAfterDiscount + this.taxAmount;
            },
            get kembalian() {
                let kembali = this.nominalBayar - this.grandTotal;
                return (kembali < 0) ? 0 : kembali;
            },
            updateNominalOnMethodChange() {
                this.$nextTick(() => {
                    if (this.metodeBayar !== 'Tunai') {
                        this.nominalBayar = this.grandTotal;
                    } else {
                        this.nominalBayar = this.grandTotal;
                    }
                });
            }
        }"
    >
        
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Konfirmasi Pembayaran</h1>
                <p class="text-sm text-gray-500">
                    Pelanggan: {{ $pelanggan->nama ?? 'Pelanggan Umum' }}
                </p>
            </div>
            <a href="{{ route('pos.index', $transaksi) }}" 
               class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200">
                &larr; Kembali ke Keranjang
            </a>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Ringkasan Belanja</h2>
            <div class="space-y-2 max-h-48 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                @foreach ($cart as $id => $item)
                    <div class="flex justify-between items-center text-sm">
                        <div>
                            <span class="font-medium text-gray-800">{{ $item->produk->nama_produk ?? $item['nama_produk'] }}</span>
                            <span class="block text-gray-500">{{ $item->jumlah ?? $item['qty'] }} x Rp {{ number_format($item->harga_satuan ?? $item['harga_jual'], 0, ',', '.') }}</span>
                        </div>
                        <span class="font-medium text-gray-900">
                            Rp {{ number_format($item->subtotal ?? ($item['qty'] * $item['harga_jual']), 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <form action="{{ route('pos.checkout.store', $transaksi) }}" method="POST">
            @csrf
            
            <input type="hidden" name="diskon_amount" x-bind:value="diskonAmount">

            <div classs="space-y-6">
                
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    <div class="flex justify-between text-md font-medium text-gray-700">
                        <span>Subtotal:</span>
                        <span x-text="'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(subtotal)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3">
                        <label for="discountInput" class="text-md font-medium text-gray-700">Diskon (Rp):</label>
                        <input type="number" id="discountInput" x-model.number.debounce.300ms="discountInput" min="0" :max="subtotal"
                               class="w-36 border-gray-300 rounded-lg shadow-sm text-right focus:ring-sky-500" />
                    </div>

                    <div class="flex justify-between text-md text-gray-700 border-t border-gray-200 pt-3">
                        <span x-text="'Pajak (PPN ' + (taxRate * 100).toFixed(0) + '%):'"></span>
                        <span x-text="'+ Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(taxAmount)"></span>
                    </div>
                </div>

                <div class="flex justify-between text-3xl font-bold text-gray-900 bg-sky-100 p-4 rounded-lg mt-6">
                    <span>Grand Total:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(grandTotal)"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="metode_bayar" class="block text-sm font-medium text-gray-700">Metode Bayar</label>
                        <select id="metode_bayar" name="metode_bayar" x-model="metodeBayar"
                                @change="updateNominalOnMethodChange()"
                                class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500">
                            <option value="Tunai">Tunai (Cash)</option>
                            <option value="Debit">Debit/QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label for="nominal_bayar" class="block text-sm font-medium text-gray-700">Nominal Bayar</label>
                        <input type="number" id="nominal_bayar" name="nominal_bayar" 
                               x-model.number="nominalBayar"
                               :min="grandTotal"
                               class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-sky-500 focus:border-sky-500 text-lg" 
                               required>
                        
                        <template x-if="nominalBayar < grandTotal && metodeBayar == 'Tunai'">
                            <p class="text-sm text-red-600 mt-1">Uang tunai kurang dari total bayar!</p>
                        </template>
                    </div>
                </div>

                <div class="flex justify-between text-xl font-semibold text-gray-800 bg-blue-50 p-4 rounded-lg mt-6"
                     x-show="metodeBayar == 'Tunai'">
                    <span>Kembalian:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(kembalian)"></span>
                </div>

                <div class="border-t pt-6 mt-6 flex justify-end">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition duration-200"
                        :disabled="nominalBayar < grandTotal && metodeBayar == 'Tunai'"
                        :class="{ 'opacity-50 cursor-not-allowed': nominalBayar < grandTotal && metodeBayar == 'Tunai' }"
                    >
                        Konfirmasi & Simpan Transaksi
                    </button>
                </div>

            </div>
        </form>

    </div>
</x-app-layout>