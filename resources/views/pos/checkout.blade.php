<x-app-layout>
    <div 
        class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-100"
        x-data="{ 
            subtotal: {{ $subtotal }}, 
            taxRate: {{ $taxRate }}, 
            discountInput: 0, 
            voucherCode: '',
            voucherMessage: '',
            voucherApplied: false,
            nominalBayar: {{ $subtotal }}, 
            
            // STATE PEMBAYARAN
            paymentCategory: 'Tunai', 
            selectedBank: null,       
            showPaymentModal: false,
            vaNumber: '',
            copyText: 'Salin',
            
            // STATE TIMER
            timeLeft: '',
            timerInterval: null,

            // DATA BANK (URL LOGO SUDAH DIPERBAIKI)
            banks: [
                { 
                    id: 'BCA', 
                    name: 'BCA Virtual Account', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg', 
                    prefix: '70001' 
                },
                { 
                    id: 'BNI', 
                    name: 'BNI Virtual Account', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Bank_Negara_Indonesia_logo_%282004%29.svg/200px-Bank_Negara_Indonesia_logo_%282004%29.svg.png?20250516061934', 
                    prefix: '8808' 
                },
                { 
                    id: 'BRI', 
                    name: 'BRIVA', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/6/68/BANK_BRI_logo.svg', 
                    prefix: '12345' 
                },
                { 
                    id: 'MANDIRI', 
                    name: 'Mandiri Bill', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg', 
                    prefix: '90000' 
                },
                { 
                    id: 'PERMATA', 
                    name: 'Permata VA', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Permata_Bank_%282024%29.svg/512px-Permata_Bank_%282024%29.svg.png', 
                    prefix: '89898' 
                },
                { 
                    id: 'CIMB', 
                    name: 'CIMB Niaga', 
                    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/38/CIMB_Niaga_logo.svg/960px-CIMB_Niaga_logo.svg.png', 
                    prefix: '5500' 
                },
            ],

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
                if (this.paymentCategory !== 'Tunai') return 0; 
                let kembali = this.nominalBayar - this.grandTotal;
                return (kembali < 0) ? 0 : kembali;
            },

            generateVANumber() {
                if (this.paymentCategory === 'VA' && this.selectedBank) {
                    let bank = this.banks.find(b => b.id === this.selectedBank);
                    this.vaNumber = bank.prefix + Math.floor(1000000000 + Math.random() * 9000000000);
                } else {
                    this.vaNumber = '';
                }
            },
            
            updateNominalOnMethodChange() {
                this.$nextTick(() => {
                    if (this.paymentCategory !== 'Tunai') {
                        this.nominalBayar = this.grandTotal;
                        this.generateVANumber();
                    }
                });
            },

            handlePayment() {
                if (this.paymentCategory === 'Tunai') {
                    this.$refs.checkoutForm.submit();
                } else if (this.paymentCategory === 'VA' && !this.selectedBank) {
                    alert('Silakan pilih Bank terlebih dahulu!');
                } else {
                    this.generateVANumber();
                    this.startTimer();
                    this.showPaymentModal = true;
                }
            },

            confirmSimulation() {
                this.$refs.checkoutForm.submit();
            },

            // TIMER LOGIC
            startTimer() {
                clearInterval(this.timerInterval);
                let endTime = new Date().getTime() + (24 * 60 * 60 * 1000); 
                
                this.timerInterval = setInterval(() => {
                    let now = new Date().getTime();
                    let distance = endTime - now;

                    if (distance < 0) {
                        clearInterval(this.timerInterval);
                        this.timeLeft = 'WAKTU HABIS';
                        return;
                    }

                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    this.timeLeft = 
                        (hours < 10 ? '0' + hours : hours) + ':' + 
                        (minutes < 10 ? '0' + minutes : minutes) + ':' + 
                        (seconds < 10 ? '0' + seconds : seconds);
                }, 1000);
            },

            // COPY LOGIC
            copyVA() {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(this.vaNumber).then(() => {
                        this.copyText = 'Tersalin!';
                        setTimeout(() => this.copyText = 'Salin', 2000);
                    }).catch(err => {
                        console.error('Gagal menyalin: ', err);
                        // Fallback manual jika HTTPS tidak aktif
                        this.fallbackCopyText(this.vaNumber);
                    });
                } else {
                    this.fallbackCopyText(this.vaNumber);
                }
            },
            
            fallbackCopyText(text) {
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.copyText = 'Tersalin!';
                    setTimeout(() => this.copyText = 'Salin', 2000);
                } catch (err) {
                    console.error('Fallback copy gagal', err);
                }
                document.body.removeChild(textArea);
            }
        }"
        x-init="updateNominalOnMethodChange()"
    >
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Checkout Transaksi</h1>
                <p class="text-sm text-gray-500 mt-1">Selesaikan pembayaran untuk invoice #{{ $transaksi->id }}</p>
            </div>
            <a href="{{ route('pos.index', $transaksi) }}" class="mt-4 sm:mt-0 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Kasir
            </a>
        </div>

        <form x-ref="checkoutForm" action="{{ route('pos.checkout.store', $transaksi) }}" method="POST">
            @csrf
            <input type="hidden" name="diskon_amount" x-bind:value="diskonAmount">
            <input type="hidden" name="metode_bayar" x-bind:value="paymentCategory === 'VA' ? 'VA ' + selectedBank : paymentCategory">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 space-y-6 order-2 lg:order-1">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 sticky top-6">
                        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Rincian Biaya</h2>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                            </div>
                            
                            <div class="pt-3 border-t border-dashed border-gray-300">
                                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kode Promo</label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" x-model="voucherCode" :disabled="voucherApplied"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 uppercase" 
                                           placeholder="KODE...">
                                    <button type="button" x-show="!voucherApplied" @click="applyVoucher()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-xs font-bold">CEK</button>
                                    <button type="button" x-show="voucherApplied" @click="resetVoucher()" class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-2 rounded-lg text-xs font-bold">HAPUS</button>
                                </div>
                                <p class="text-xs" :class="voucherApplied ? 'text-green-600' : 'text-red-500'" x-text="voucherMessage"></p>
                            </div>

                            <div class="flex justify-between text-red-600" x-show="diskonAmount > 0">
                                <span>Diskon</span>
                                <span class="font-medium" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(diskonAmount)"></span>
                            </div>

                            <div class="flex justify-between text-gray-600">
                                <span x-text="'PPN (' + (taxRate * 100).toFixed(0) + '%)'"></span>
                                <span class="font-medium text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(taxAmount)"></span>
                            </div>
                            
                            <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                                <span class="text-base font-bold text-gray-800">Total Tagihan</span>
                                <span class="text-xl font-bold text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                            </div>
                        </div>

                        <button type="button" @click="handlePayment()"
                            class="w-full mt-6 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="nominalBayar < grandTotal && paymentCategory == 'Tunai'">
                            <span x-text="paymentCategory === 'Tunai' ? 'Bayar Tunai Sekarang' : 'Lanjut Pembayaran'"></span>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6 order-1 lg:order-2">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" 
                                @click="paymentCategory = 'Tunai'; selectedBank = null; updateNominalOnMethodChange()"
                                :class="{'bg-indigo-50 border-indigo-600 text-indigo-700 ring-1 ring-indigo-600': paymentCategory === 'Tunai', 'bg-white border-gray-200 hover:border-gray-300 text-gray-600': paymentCategory !== 'Tunai'}"
                                class="flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="text-sm font-semibold">Tunai</span>
                            </button>

                            <button type="button" 
                                @click="paymentCategory = 'QRIS'; selectedBank = null; updateNominalOnMethodChange()"
                                :class="{'bg-indigo-50 border-indigo-600 text-indigo-700 ring-1 ring-indigo-600': paymentCategory === 'QRIS', 'bg-white border-gray-200 hover:border-gray-300 text-gray-600': paymentCategory !== 'QRIS'}"
                                class="flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                <span class="text-sm font-semibold">QRIS</span>
                            </button>

                            <button type="button" 
                                @click="paymentCategory = 'VA'; updateNominalOnMethodChange()"
                                :class="{'bg-indigo-50 border-indigo-600 text-indigo-700 ring-1 ring-indigo-600': paymentCategory === 'VA', 'bg-white border-gray-200 hover:border-gray-300 text-gray-600': paymentCategory !== 'VA'}"
                                class="flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-sm font-semibold">Virtual Account</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="paymentCategory === 'Tunai'" x-transition>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                            <input type="number" name="nominal_bayar" x-model.number="nominalBayar" :min="grandTotal"
                                   class="block w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3 text-lg font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="0">
                        </div>
                        <div class="mt-3 flex justify-between items-center bg-gray-100 p-3 rounded-lg border border-gray-200">
                            <span class="text-sm text-gray-600">Kembalian:</span>
                            <span class="text-lg font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian)"></span>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" @click="nominalBayar = grandTotal" class="text-xs bg-white border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50">Uang Pas</button>
                            <button type="button" @click="nominalBayar = Math.ceil(grandTotal / 10000) * 10000" class="text-xs bg-white border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50">Bulatkan 10rb</button>
                            <button type="button" @click="nominalBayar = Math.ceil(grandTotal / 50000) * 50000" class="text-xs bg-white border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50">Bulatkan 50rb</button>
                        </div>
                    </div>

                    <div x-show="paymentCategory === 'VA'" x-transition>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Bank</label>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <template x-for="bank in banks" :key="bank.id">
                                <button type="button" 
                                    @click="selectedBank = bank.id"
                                    :class="selectedBank === bank.id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300 bg-white'"
                                    class="relative flex flex-col items-center justify-center h-24 border rounded-xl transition-all duration-200 p-4 group">
                                    
                                    <div x-show="selectedBank === bank.id" class="absolute top-2 right-2 text-blue-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    </div>

                                    <img :src="bank.logo" :alt="bank.name" class="h-8 object-contain mb-2">
                                    
                                    <span class="text-[10px] text-gray-500 text-center font-medium" x-text="bank.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div x-show="paymentCategory === 'QRIS'" x-transition class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-center">
                        <p class="text-sm text-gray-600">QR Code akan muncul setelah Anda menekan tombol "Lanjut Pembayaran".</p>
                    </div>

                </div>
            </div>
        </form>

        <div x-show="showPaymentModal" style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             x-transition.opacity>
            
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden relative animate-fade-in-up">
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg">Selesaikan Pembayaran</h3>
                    </div>
                    <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-end border-b border-gray-100 pb-4">
                        <span class="text-sm text-gray-500">Total Tagihan</span>
                        <span class="text-2xl font-bold text-gray-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                    </div>

                    <div x-show="paymentCategory === 'QRIS'" class="text-center py-4">
                        <div class="bg-white p-2 inline-block border border-gray-200 rounded-xl shadow-sm mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=SimulasiBayarMenuKhas" alt="QRIS Code" class="w-40 h-40 rounded-lg">
                        </div>
                        <div class="flex items-center justify-center gap-2 text-gray-600">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/1200px-Logo_QRIS.svg.png" class="h-6" alt="QRIS">
                            <span class="text-sm font-medium">NMID: ID123456789</span>
                        </div>
                    </div>

                    <div x-show="paymentCategory === 'VA'" class="space-y-4">
                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider" x-text="selectedBank + ' Virtual Account'"></span>
                                <template x-if="selectedBank">
                                    <img :src="banks.find(b => b.id === selectedBank).logo" class="h-5 w-auto object-contain" alt="Bank Logo">
                                </template>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-mono text-2xl font-bold text-gray-800 tracking-widest" x-text="vaNumber"></span>
                                <button @click="copyVA()" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold transition-colors focus:outline-none" x-text="copyText">Salin</button>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 text-center">
                            Nomor VA ini hanya berlaku untuk simulasi transaksi ini.
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <div class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sisa waktu bayar: <span x-text="timeLeft" class="font-bold font-mono ml-1"></span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button @click="confirmSimulation()" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-green-200 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Saya Sudah Bayar
                        </button>
                        <button @click="showPaymentModal = false" 
                                class="w-full bg-white border border-gray-200 text-gray-600 font-semibold py-3 px-4 rounded-xl hover:bg-gray-50 transition-colors">
                            Ganti Metode Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>