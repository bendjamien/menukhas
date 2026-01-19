<x-app-layout>
    <div x-data="{ showCancelModal: false }" class="min-h-screen bg-gray-50 flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8 relative">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md z-10">
            <div class="mx-auto h-12 w-12 bg-sky-100 rounded-full flex items-center justify-center mb-4">
                <svg class="h-6 w-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900">
                Selesaikan Pembayaran
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Order ID: <span class="font-mono font-bold">{{ $transaksi->catatan ?? 'POS-'.$transaksi->id }}</span>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md z-10">
            <div class="bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-gray-100">
                
                <div class="text-center mb-8 pb-8 border-b border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-2">Total Yang Harus Dibayar</p>
                    <p class="text-4xl font-extrabold text-sky-600">
                        Rp {{ number_format($transaksi->total, 0, ',', '.') }}
                    </p>
                </div>

                <div class="space-y-4">
                    <button id="pay-button" 
                            class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        Pilih Metode Pembayaran (QRIS / VA)
                    </button>

                    <button type="button" 
                            @click="showCancelModal = true"
                            class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Batalkan Transaksi
                    </button>
                </div>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Didukung oleh Midtrans</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showCancelModal" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCancelModal = false"></div>

            <div class="bg-white rounded-2xl overflow-hidden shadow-xl transform transition-all sm:max-w-sm w-full p-6 relative z-50 border border-gray-100">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Batalkan Transaksi?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Stok barang yang sudah dipesan akan dikembalikan ke gudang. Transaksi ini akan dihapus.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 grid grid-cols-2 gap-3">
                    <button type="button" 
                            @click="showCancelModal = false"
                            class="w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                        Tidak, Kembali
                    </button>
                    <a href="{{ route('pos.cancel_pending', $transaksi->id) }}" 
                       class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm">
                        Ya, Batalkan
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    <script type="text/javascript">
        const payButton = document.getElementById('pay-button');

        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    window.location.href = "{{ route('pos.payment_success', $transaksi->id) }}";
                },
                onPending: function(result){
                    window.location.href = "{{ route('pos.index') }}?status=pending";
                },
                onError: function(result){
                    window.location.href = "{{ route('pos.index') }}?status=error";
                },
                onClose: function(){
                }
            });
        });
    </script>

</x-app-layout>