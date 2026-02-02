<x-app-layout>
    <!-- 
        Container Utama: 
        - Menggunakan h-[calc(100vh-65px)] agar pas di bawah navbar.
        - overflow-hidden agar tidak ada scrollbar.
    -->
    <div x-data="{ showCancelModal: false }" class="relative w-full h-[calc(100vh-65px)] overflow-hidden bg-slate-50 flex flex-col">
        
        <!-- Background Decor -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -left-24 w-96 h-96 bg-sky-400/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-col md:flex-row h-full w-full">
            
            <!-- LEFT SIDE: Billing Info -->
            <div class="w-full md:w-1/2 lg:w-5/12 bg-white border-r border-slate-100 p-6 md:p-10 flex flex-col justify-center shadow-2xl z-20">
                <div class="max-w-md mx-auto w-full">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-sky-600 rounded-xl flex items-center justify-center shadow-lg shadow-sky-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Checkout POS</h1>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Digital Payment</p>
                        </div>
                    </div>

                    <div class="mb-10">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Pembayaran</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-medium text-slate-400">Rp</span>
                            <span class="text-5xl lg:text-6xl font-black text-slate-900 tracking-tighter">
                                {{ number_format($transaksi->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-mono font-bold text-slate-600 border border-slate-200">
                                #{{ $transaksi->catatan ?? 'POS-'.$transaksi->id }}
                            </span>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold border border-emerald-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Menunggu Bayar
                            </span>
                        </div>
                    </div>

                    <!-- Logo Pembayaran (URL Updated to Global Wikimedia) -->
                    <div class="pt-8 border-t border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-4">Metode Pembayaran:</p>
                        <div class="grid grid-cols-4 gap-x-6 gap-y-4 items-center opacity-80">
                            <!-- BCA -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" class="h-4 object-contain" alt="BCA">
                            
                            <!-- BRI -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/BANK_BRI_logo.svg/2560px-BANK_BRI_logo.svg.png" class="h-6 object-contain" alt="BRI">
                            
                            <!-- Mandiri -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/2560px-Bank_Mandiri_logo_2016.svg.png" class="h-6 object-contain" alt="Mandiri">
                            
                            <!-- BNI (NEW URL) -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c6/Logo_Wondr_by_BNI.svg" class="h-5 object-contain" alt="BNI">
                            
                            <!-- QRIS (NEW URL) -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" class="h-6 object-contain" alt="QRIS">
                            
                            <!-- OVO -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_ovo_purple.svg/2560px-Logo_ovo_purple.svg.png" class="h-4 object-contain" alt="OVO">
                            
                            <!-- DANA -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Logo_dana_blue.svg/2560px-Logo_dana_blue.svg.png" class="h-4 object-contain" alt="DANA">
                            
                            <!-- LinkAja -->
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/LinkAja.svg/2560px-LinkAja.svg.png" class="h-5 object-contain" alt="LinkAja">
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Payment Trigger -->
            <div class="flex-1 bg-slate-50 flex flex-col justify-center items-center p-8 relative">
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"></div>

                <div class="relative z-10 w-full max-w-sm text-center">
                    <div class="mb-10">
                        <div class="w-24 h-24 bg-white rounded-3xl mx-auto flex items-center justify-center shadow-xl mb-6 ring-8 ring-sky-100/50 rotate-3 hover:rotate-0 transition-transform duration-500">
                            <svg class="w-12 h-12 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Siap Membayar?</h2>
                        <p class="text-slate-500 mt-2 text-sm">Gunakan <b>QRIS</b> atau <b>Transfer Bank</b> untuk memproses pesanan.</p>
                    </div>

                    <button id="pay-button" 
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white text-lg font-bold py-5 px-8 rounded-2xl shadow-2xl shadow-sky-500/30 transform hover:-translate-y-1 active:scale-95 transition-all duration-200 flex items-center justify-center gap-3 group">
                        <span>BAYAR SEKARANG</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>

                    <button @click="showCancelModal = true" class="mt-8 text-slate-400 hover:text-red-500 text-xs font-bold uppercase tracking-widest transition-colors flex items-center justify-center gap-2 mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Batalkan Transaksi
                    </button>
                </div>

                <div class="absolute bottom-6 flex items-center gap-2 text-slate-400 font-bold text-[10px] tracking-[0.2em] uppercase">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    <span>Secure Gateway by Midtrans</span>
                </div>
            </div>

        </div>

        <!-- Cancel Modal -->
        <div x-show="showCancelModal" 
             style="display: none;"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 transform transition-all scale-100 text-center"
                 @click.away="showCancelModal = false">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h3 class="text-xl font-bold text-slate-900 mb-2">Batalkan?</h3>
                <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                    Stok barang akan dikembalikan dan transaksi akan dihapus.
                </p>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('pos.cancel_pending', $transaksi->id) }}" class="w-full py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 text-sm">
                        Ya, Batalkan
                    </a>
                    <button @click="showCancelModal = false" class="w-full py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition text-sm">
                        Kembali
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script type="text/javascript">
        const payButton = document.getElementById('pay-button');
        let checkInterval;

        // Auto-Check Status setiap 3 detik (Polling)
        function startPolling() {
            checkInterval = setInterval(() => {
                fetch('{{ route("pos.check_status", $transaksi->id) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'selesai') {
                            clearInterval(checkInterval);
                            Toastify({ text: "Pembayaran Berhasil! Mengalihkan...", duration: 2000, style: { background: "#10b981" } }).showToast();
                            setTimeout(() => {
                                window.location.href = "{{ route('pos.payment_success', $transaksi->id) }}";
                            }, 1500);
                        } else if (data.status === 'batal') {
                            clearInterval(checkInterval);
                            window.location.href = "{{ route('pos.index') }}?status=batal";
                        }
                    })
                    .catch(err => console.error(err));
            }, 3000); 
        }

        payButton.addEventListener('click', function () {
            // Mulai polling segera setelah tombol bayar diklik (asumsi user mulai proses bayar)
            startPolling();

            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    payButton.innerHTML = '<span class="animate-spin mr-2">↻</span> Memproses...';
                    window.location.href = "{{ route('pos.payment_success', $transaksi->id) }}";
                },
                onPending: function(result){
                    // Biarkan polling berjalan
                },
                onError: function(result){
                    Toastify({ text: "Pembayaran Gagal!", duration: 3000, style: { background: "#ef4444" } }).showToast();
                },
                onClose: function(){
                    // Tetap lanjutkan polling karena bisa saja user sudah bayar tapi baru tutup popup
                    Toastify({ text: "Menunggu konfirmasi pembayaran...", duration: 3000, style: { background: "#f59e0b" } }).showToast();
                }
            });
        });
    </script>
</x-app-layout>