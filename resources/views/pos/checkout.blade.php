<x-app-layout>
    <div 
        class="max-w-4xl mx-auto bg-white p-6 md:p-8 rounded-xl shadow-lg"
        x-data="{ 
            subtotal: {{ $subtotal }}, 
            taxRate: {{ $taxRate }}, 
            discountInput: 0, 
            voucherCode: '',
            voucherMessage: '',
            voucherApplied: false,
            nominalBayar: {{ $subtotal }}, 
            metodeBayar: 'Tunai',
            showPaymentModal: false,
            vaNumber: '880' + Math.floor(Math.random() * 1000000000),

            async applyVoucher() {
                if (!this.voucherCode) return;
                try {
                    let response = await fetch('{{ route('pos.check_voucher') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ voucher_code: this.voucherCode, subtotal: this.subtotal })
                    });
                    let data = await response.json();
                    if (data.valid) {
                        this.discountInput = data.discount_amount;
                        this.voucherMessage = data.message;
                        this.voucherApplied = true;
                    } else {
                        this.discountInput = 0;
                        this.voucherMessage = data.message;
                        this.voucherApplied = false;
                    }
                } catch (error) { console.error('Error:', error); }
            },

            resetVoucher() {
                this.discountInput = 0; this.voucherCode = ''; this.voucherMessage = ''; this.voucherApplied = false;
            },

            get diskonAmount() { return parseFloat(this.discountInput) || 0; },
            get totalAfterDiscount() { return this.subtotal - this.diskonAmount; },
            get taxAmount() { return this.totalAfterDiscount * this.taxRate; },
            get grandTotal() { return this.totalAfterDiscount + this.taxAmount; },
            get kembalian() { 
                if (this.metodeBayar !== 'Tunai') return 0; 
                let kembali = this.nominalBayar - this.grandTotal;
                return (kembali < 0) ? 0 : kembali;
            },
            
            updateNominalOnMethodChange() {
                this.$nextTick(() => {
                    if (this.metodeBayar === 'Tunai') {
                        this.nominalBayar = this.grandTotal;
                    } else {
                        this.nominalBayar = this.grandTotal;
                        // Generate VA baru untuk efek simulasi
                        this.vaNumber = '880' + Math.floor(Math.random() * 1000000000);
                    }
                });
            },

            handlePayment() {
                if (this.metodeBayar === 'Tunai') {
                    this.$refs.checkoutForm.submit();
                } else {
                    this.showPaymentModal = true;
                }
            },

            confirmSimulation() {
                this.$refs.checkoutForm.submit();
            }
        }"
    >
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-200">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Konfirmasi Pembayaran</h1>
            <a href="{{ route('pos.index', $transaksi) }}" class="inline-flex items-center text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <form x-ref="checkoutForm" action="{{ route('pos.checkout.store', $transaksi) }}" method="POST">
            @csrf
            <input type="hidden" name="diskon_amount" x-bind:value="diskonAmount">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Rincian Pembayaran -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Rincian Pembayaran</h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-md font-medium text-gray-700">Subtotal:</span>
                                <span class="text-md font-medium" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4">
                                <label class="text-sm font-medium text-gray-700 mb-2 block">Kode Voucher (Opsional):</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="voucherCode" :disabled="voucherApplied"
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase" 
                                           placeholder="Masukan kode...">
                                    <button type="button" x-show="!voucherApplied" @click="applyVoucher()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">Gunakan</button>
                                    <button type="button" x-show="voucherApplied" @click="resetVoucher()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">Batal</button>
                                </div>
                                <p class="text-xs mt-2" :class="voucherApplied ? 'text-green-600' : 'text-red-500'" x-text="voucherMessage"></p>
                            </div>

                            <div class="flex justify-between items-center text-md text-red-600 border-t border-gray-200 pt-4" x-show="diskonAmount > 0">
                                <span class="font-medium">Diskon:</span>
                                <span class="font-medium" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(diskonAmount)"></span>
                            </div>

                            <div class="flex justify-between items-center text-md text-gray-700 border-t border-gray-200 pt-4">
                                <span x-text="'Pajak (PPN ' + (taxRate * 100).toFixed(0) + '%):'"></span>
                                <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(taxAmount)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-gray-50 p-6 rounded-xl shadow-sm">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">Metode Pembayaran</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Metode</label>
                                <select name="metode_bayar" x-model="metodeBayar" @change="updateNominalOnMethodChange()"
                                        class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Tunai">Tunai (Cash)</option>
                                    <option value="QRIS">QRIS (Scan)</option> 
                                    <option value="VA BCA">Virtual Account BCA</option>
                                    <option value="VA Mandiri">Virtual Account Mandiri</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Bayar</label>
                                <input type="number" name="nominal_bayar" x-model.number="nominalBayar" :min="grandTotal"
                                       :readonly="metodeBayar !== 'Tunai'"
                                       :class="{'bg-gray-100 cursor-not-allowed': metodeBayar !== 'Tunai', 'bg-white': metodeBayar === 'Tunai'}"
                                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 text-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total dan Kembalian -->
                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-xl shadow-md text-white">
                        <h2 class="text-lg font-semibold mb-4 opacity-90">Total Pembayaran</h2>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm opacity-90">Grand Total:</span>
                            <span class="text-2xl font-bold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-blue-400" x-show="metodeBayar == 'Tunai'">
                            <span class="text-sm opacity-90">Kembalian:</span>
                            <span class="text-xl font-semibold" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian)">Rp 0</span>
                        </div>
                    </div>

                    <button type="button" @click="handlePayment()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105"
                        :disabled="nominalBayar < grandTotal && metodeBayar == 'Tunai'"
                        :class="{ 'opacity-50 cursor-not-allowed': nominalBayar < grandTotal && metodeBayar == 'Tunai' }">
                        <span x-text="metodeBayar === 'Tunai' ? 'Konfirmasi & Bayar' : 'Lanjut ke Pembayaran'"></span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Payment Modal -->
        <div x-show="showPaymentModal" style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showPaymentModal = false">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Simulasi Pembayaran Aman
                    </h3>
                    <button @click="showPaymentModal = false" class="text-white hover:text-gray-200 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="text-center bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-500 text-sm mb-1">Total Tagihan</p>
                        <p class="text-3xl font-bold text-blue-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></p>
                    </div>

                    <div x-show="metodeBayar === 'QRIS'" class="text-center space-y-4">
                        <div class="bg-white p-4 inline-block border-4 border-gray-800 rounded-lg shadow-lg">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MenuKhasPaymentGatewaySimulation" 
                                 alt="QRIS Code" class="w-48 h-48">
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-lg">Scan QRIS</p>
                            <p class="text-sm text-gray-500">NMID: ID1234567890123</p>
                        </div>
                    </div>

                    <div x-show="metodeBayar !== 'QRIS'" class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-center">
                        <p class="text-sm text-gray-600 font-medium mb-2" x-text="'Nomor ' + metodeBayar"></p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="font-mono text-2xl font-bold tracking-wider text-gray-800" x-text="vaNumber"></span>
                            <button class="text-gray-400 hover:text-blue-600 transition-colors" title="Salin">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-3 bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full inline-block">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Selesaikan pembayaran dalam 23:59:59
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button @click="confirmSimulation()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simulasi Bayar Sukses
                        </button>
                        <button @click="showPaymentModal = false" 
                                class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                            Ganti Metode Pembayaran
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-100 px-6 py-3 text-center">
                    <p class="text-xs text-gray-500 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Secured by MenuKhas Payment Gateway
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>