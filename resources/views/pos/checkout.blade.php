<x-app-layout>
    @if ($errors->any() || session('toast_danger'))
        <div class="max-w-5xl mx-auto mb-4 px-6 md:px-8">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm flex items-start">
                <svg class="h-5 w-5 text-red-400 mt-0.5 mr-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-red-700 font-bold">{{ session('toast_danger') ?? 'Terjadi kesalahan input.' }}</p>
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600 mt-1">- {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div 
        class="max-w-5xl mx-auto bg-white p-6 md:p-8 rounded-2xl shadow-xl border border-gray-100"
        x-data="{ 
            subtotal: {{ $subtotal }}, 
            taxRate: {{ $taxRate }}, 
            voucherCode: '',
            voucherMessage: '',
            voucherApplied: false,
            discountInput: 0,
            
            // Logic Pembayaran
            paymentMethod: 'Tunai', 
            uiSelection: 'Tunai',
            nominalBayar: 0,
            
            // Logic Poin
            poinAvailable: {{ $pelanggan ? $pelanggan->poin : 0 }},
            pointsValue: {{ \App\Models\Setting::where('key', 'loyalty_nilai_rupiah_per_poin')->value('value') ?? 0 }},
            poinUsed: 0,

            // Logic Member
            memberPhone: '{{ $pelanggan ? $pelanggan->no_hp : "" }}',
            memberFound: {{ $pelanggan ? 'true' : 'false' }},
            memberName: '{{ $pelanggan ? $pelanggan->nama : "" }}',
            memberId: '{{ $pelanggan ? $pelanggan->id : "" }}',
            searchLoading: false,

            async checkMemberByPhone() {
                if (!this.memberPhone) {
                    Toastify({ text: 'Masukkan nomor HP!', duration: 3000, style: { background: '#ef4444' } }).showToast();
                    return;
                }
                this.searchLoading = true;
                
                try {
                    let response = await fetch(`{{ route('pos.search_member') }}?no_hp=${this.memberPhone}`);
                    let data = await response.json();
                    
                    if (data.valid) {
                        this.memberFound = true;
                        this.memberName = data.member.nama;
                        this.poinAvailable = data.member.poin;
                        this.memberId = data.member.id;
                        this.poinUsed = 0; 
                        Toastify({ text: 'Member Ditemukan: ' + data.member.nama, duration: 3000, style: { background: '#10b981' } }).showToast();
                    } else {
                        Toastify({ text: 'Member tidak ditemukan!', duration: 3000, style: { background: '#ef4444' } }).showToast();
                        this.memberFound = false;
                        this.memberName = '';
                        this.poinAvailable = 0;
                        this.memberId = '';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Toastify({ text: 'Terjadi kesalahan sistem.', duration: 3000, style: { background: '#ef4444' } }).showToast();
                } finally {
                    this.searchLoading = false;
                }
            },

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
                this.discountInput = 0; 
                this.voucherCode = ''; 
                this.voucherMessage = ''; 
                this.voucherApplied = false;
            },

            validatePoin() {
                let max = this.poinAvailable;
                if (this.poinUsed > max) this.poinUsed = max;
                if (this.poinUsed < 0) this.poinUsed = 0;
            },

            get diskonAmount() { return parseFloat(this.discountInput) || 0; },
            
            get poinDiscountAmount() { 
                return (parseInt(this.poinUsed) || 0) * this.pointsValue; 
            },

            get totalAfterDiscount() { 
                let total = this.subtotal - this.diskonAmount - this.poinDiscountAmount;
                return total < 0 ? 0 : total;
            },

            get taxAmount() { return this.totalAfterDiscount * this.taxRate; },
            
            get grandTotal() { return Math.round(this.totalAfterDiscount + this.taxAmount); },
            
            get kembalian() { 
                if (this.paymentMethod !== 'Tunai') return 0; 
                let kembali = this.nominalBayar - this.grandTotal;
                return (kembali < 0) ? 0 : kembali;
            },

            selectMethod(backendValue, uiValue) {
                this.paymentMethod = backendValue;
                this.uiSelection = uiValue;
                
                if (backendValue !== 'Tunai') {
                    this.nominalBayar = this.grandTotal;
                } else {
                    this.nominalBayar = 0; 
                }
            }
        }"
        x-init="selectMethod('Tunai', 'Tunai')" 
    >
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Checkout</h1>
                <p class="text-sm text-gray-500 mt-1">Invoice <span class="font-mono font-bold text-indigo-600">#{{ $transaksi->id }}</span></p>
            </div>
            <a href="{{ route('pos.index', $transaksi) }}" class="mt-4 sm:mt-0 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Kasir
            </a>
        </div>

        <form x-ref="checkoutForm" action="{{ route('pos.checkout.store', $transaksi) }}" method="POST">
            @csrf
            <!-- Hidden Inputs Sync -->
            <input type="hidden" name="voucher_code" x-bind:value="voucherCode">
            <input type="hidden" name="metode_bayar" x-bind:value="paymentMethod">
            <input type="hidden" name="nominal_bayar" x-bind:value="nominalBayar">
            <input type="hidden" name="poin_tukar" x-bind:value="poinUsed">
            <input type="hidden" name="pelanggan_id" x-bind:value="memberId">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KOLOM KANAN: RINGKASAN BIAYA (Sticky on desktop) -->
                <div class="lg:col-span-1 space-y-6 order-2 lg:order-2">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 sticky top-6">
                        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Ringkasan Biaya</h2>
                        
                        <div class="space-y-3 text-sm">
                            <!-- Subtotal -->
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal)"></span>
                            </div>
                            
                            <!-- Cek Member / Poin -->
                            <div class="pt-3 border-t border-dashed border-gray-300">
                                <label class="text-xs font-semibold text-gray-500 mb-1 block">Cek Member (Poin)</label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" x-model="memberPhone" @keydown.enter.prevent="checkMemberByPhone()"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500" 
                                            placeholder="No. HP (08xx)...">
                                    <button type="button" @click="checkMemberByPhone()" :disabled="searchLoading"
                                            class="bg-sky-600 hover:bg-sky-700 text-white px-3 py-2 rounded-lg text-xs font-bold disabled:opacity-50">
                                        <span x-show="!searchLoading">CEK</span>
                                        <span x-show="searchLoading">...</span>
                                    </button>
                                </div>

                                <!-- Hasil Cek Member -->
                                <div x-show="memberFound" class="bg-amber-50 p-3 rounded-lg border border-amber-200 mt-2">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-bold text-amber-800" x-text="memberName"></span>
                                        <span class="text-[10px] bg-amber-200 text-amber-800 px-2 py-1 rounded-full font-bold" x-text="poinAvailable + ' Pts'"></span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2" x-show="poinAvailable > 0">
                                        <input type="number" x-model="poinUsed" @input="validatePoin()" :max="poinAvailable" min="0"
                                               class="w-20 border-amber-300 rounded-lg px-2 py-1 text-sm text-right focus:ring-amber-500 shadow-sm">
                                        <span class="text-xs text-gray-500">Pts</span>
                                        <span class="ml-auto text-xs font-bold text-amber-600" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(poinDiscountAmount)"></span>
                                    </div>
                                    <p x-show="poinAvailable <= 0" class="text-xs text-amber-600 italic">Poin tidak cukup.</p>
                                </div>
                            </div>

                            <!-- Voucher Input -->
                            <div class="pt-3 border-t border-dashed border-gray-300">
                                <label class="text-xs font-semibold text-gray-500 mb-1 block">Kode Promo</label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" x-model="voucherCode" :disabled="voucherApplied"
                                            @keydown.enter.prevent="applyVoucher()"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 uppercase" 
                                            placeholder="KODE...">
                                    <button type="button" 
                                            @click="voucherApplied ? resetVoucher() : applyVoucher()"
                                            class="px-3 py-2 rounded-lg text-xs font-bold shrink-0 transition-colors"
                                            :class="voucherApplied ? 'bg-red-100 hover:bg-red-200 text-red-600' : 'bg-indigo-600 hover:bg-indigo-700 text-white'">
                                        <span x-text="voucherApplied ? 'HAPUS' : 'CEK'"></span>
                                    </button>
                                </div>
                                <p class="text-xs" :class="voucherApplied ? 'text-green-600' : 'text-red-500'" x-text="voucherMessage"></p>
                            </div>

                            <!-- Diskon Display -->
                            <div class="flex justify-between text-red-600" x-show="diskonAmount > 0">
                                <span>Diskon Voucher</span>
                                <span class="font-medium" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(diskonAmount)"></span>
                            </div>

                            <div class="flex justify-between text-amber-600" x-show="poinDiscountAmount > 0">
                                <span>Potongan Poin</span>
                                <span class="font-medium" x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(poinDiscountAmount)"></span>
                            </div>

                            <!-- Pajak -->
                            <div class="flex justify-between text-gray-600">
                                <span x-text="'PPN (' + (taxRate * 100).toFixed(0) + '%)'"></span>
                                <span class="font-medium text-gray-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(taxAmount)"></span>
                            </div>
                            
                            <!-- Grand Total -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                                <span class="text-base font-bold text-gray-800">Total Tagihan</span>
                                <span class="text-2xl font-bold text-sky-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                            </div>
                        </div>

                        <!-- Tombol Bayar -->
                        <button type="submit"
                            class="w-full mt-6 font-bold py-4 px-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed text-white flex justify-center items-center gap-2"
                            :class="paymentMethod === 'Tunai' ? 'bg-sky-600 hover:bg-sky-700 shadow-indigo-200' : 'bg-sky-600 hover:bg-sky-700 shadow-sky-200'"
                            :disabled="paymentMethod === 'Tunai' && nominalBayar < grandTotal">
                            
                            <svg x-show="paymentMethod === 'Tunai'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <svg x-show="paymentMethod !== 'Tunai'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            
                            <span x-show="paymentMethod === 'Tunai'">Bayar Tunai</span>
                            <span x-show="paymentMethod !== 'Tunai'">Lanjut Pembayaran</span>
                        </button>
                    </div>
                </div>

                <!-- KOLOM KIRI: METODE PEMBAYARAN -->
                <div class="lg:col-span-2 space-y-6 order-1 lg:order-1">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            <!-- Tunai -->
                            <button type="button" 
                                @click="selectMethod('Tunai', 'Tunai')"
                                :class="uiSelection === 'Tunai' ? 'bg-indigo-50 border-indigo-500 ring-2 ring-indigo-200 text-indigo-700' : 'bg-white border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600'"
                                class="relative flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200 h-28 group">
                                <div class="p-2 rounded-full mb-2" :class="uiSelection === 'Tunai' ? 'bg-indigo-100' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-6 h-6" :class="uiSelection === 'Tunai' ? 'text-indigo-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="text-sm font-bold">Tunai / Cash</span>
                                <div class="absolute top-3 right-3" x-show="uiSelection === 'Tunai'">
                                    <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </button>

                            <!-- QRIS -->
                            <button type="button" 
                                @click="selectMethod('Midtrans', 'QRIS')"
                                :class="uiSelection === 'QRIS' ? 'bg-sky-50 border-sky-500 ring-2 ring-sky-200 text-sky-700' : 'bg-white border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600'"
                                class="relative flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200 h-28 group">
                                <div class="p-2 rounded-full mb-2" :class="uiSelection === 'QRIS' ? 'bg-sky-100' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-6 h-6" :class="uiSelection === 'QRIS' ? 'text-sky-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                </div>
                                <span class="text-sm font-bold">QRIS / E-Wallet</span>
                                <span class="text-[10px] text-gray-400 mt-1">Gopay, OVO, Shopee</span>
                                <div class="absolute top-3 right-3" x-show="uiSelection === 'QRIS'">
                                    <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </button>

                            <!-- Transfer Bank -->
                            <button type="button" 
                                @click="selectMethod('Midtrans', 'VA')"
                                :class="uiSelection === 'VA' ? 'bg-sky-50 border-sky-500 ring-2 ring-sky-200 text-sky-700' : 'bg-white border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600'"
                                class="relative flex flex-col items-center justify-center p-4 border rounded-xl transition-all duration-200 h-28 group">
                                <div class="p-2 rounded-full mb-2" :class="uiSelection === 'VA' ? 'bg-sky-100' : 'bg-gray-100 group-hover:bg-gray-200'">
                                    <svg class="w-6 h-6" :class="uiSelection === 'VA' ? 'text-sky-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <span class="text-sm font-bold">Transfer Bank</span>
                                <span class="text-[10px] text-gray-400 mt-1">BCA, BRI, Mandiri, BNI</span>
                                <div class="absolute top-3 right-3" x-show="uiSelection === 'VA'">
                                    <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- INPUT TUNAI -->
                    <div x-show="paymentMethod === 'Tunai'" x-transition>
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Uang Diterima</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">Rp</span>
                                <input type="number" x-model.number="nominalBayar" :min="grandTotal"
                                    class="block w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3 text-lg font-bold focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" 
                                    :class="{'border-red-500 focus:border-red-500 focus:ring-red-200': {{ $errors->has('nominal_bayar') ? 'true' : 'false' }} }"
                                    placeholder="0">
                            </div>
                            @error('nominal_bayar')
                                <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                            
                            <div class="flex gap-2 mt-3 mb-4">
                                <button type="button" @click="nominalBayar = grandTotal" class="text-xs font-medium bg-white border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">Uang Pas</button>
                                <button type="button" @click="nominalBayar = Math.ceil(grandTotal / 10000) * 10000" class="text-xs font-medium bg-white border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">Bulatkan 10rb</button>
                                <button type="button" @click="nominalBayar = Math.ceil(grandTotal / 50000) * 50000" class="text-xs font-medium bg-white border border-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">Bulatkan 50rb</button>
                            </div>

                            <div class="mt-4 flex justify-between items-center bg-white p-4 rounded-lg border border-gray-200 shadow-inner">
                                <span class="text-sm font-medium text-gray-500">Kembalian</span>
                                <span class="text-xl font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(kembalian)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- INPUT NON-TUNAI -->
                    <div x-show="paymentMethod !== 'Tunai'" x-transition 
                         class="bg-sky-50 p-6 rounded-xl border border-sky-100 text-center flex flex-col items-center">
                        
                        <div class="w-12 h-12 bg-sky-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        
                        <h3 class="text-sky-800 font-bold mb-1">Pembayaran Digital Dipilih</h3>
                        <p class="text-sm text-sky-600 mb-4 px-8">
                            Klik tombol <strong>"Lanjut Pembayaran"</strong>. Anda akan diarahkan ke halaman aman untuk menyelesaikan pembayaran via <span x-text="uiSelection"></span>.
                        </p>
                    </div>

                </div>
            </div>
        </form>

    </div>
</x-app-layout>