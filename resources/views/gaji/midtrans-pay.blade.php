<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-[60vh] space-y-6">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center max-w-md w-full">
            <div class="w-20 h-20 bg-sky-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-black text-slate-800 mb-2">Pembayaran Gaji</h2>
            <p class="text-slate-500 font-medium mb-6">Silakan selesaikan pembayaran gaji untuk <b>{{ $penggajian->user->name }}</b> melalui portal Midtrans.</p>
            
            <div class="bg-slate-50 rounded-2xl p-4 mb-8">
                <div class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Dibayarkan</div>
                <div class="text-3xl font-black text-sky-600">Rp {{ number_format($penggajian->total_diterima, 0, ',', '.') }}</div>
            </div>

            <button id="pay-button" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-4 rounded-xl shadow-lg shadow-sky-100 uppercase tracking-widest transition-all mb-4">
                Bayar Sekarang
            </button>

            <a href="{{ route('gaji.index') }}" class="block text-slate-400 font-bold text-sm hover:text-slate-600 transition-colors">
                Kembali ke Daftar Gaji
            </a>
        </div>
    </div>

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            const payButton = document.getElementById('pay-button');
            payButton.addEventListener('click', function () {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('gaji.payment_success', $penggajian->id) }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('gaji.index') }}";
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function() {
                        alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                    }
                });
            });

            // Polling status di background
            setInterval(function() {
                fetch("{{ route('gaji.check_status', $penggajian->id) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'dibayar') {
                            window.location.href = "{{ route('gaji.payment_success', $penggajian->id) }}";
                        }
                    });
            }, 5000);
        </script>
    @endpush
</x-app-layout>
