<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Konfirmasi Pembayaran</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="text-center mb-6">
                    <p class="text-4xl mb-2">💳</p>
                    <h3 class="text-xl font-bold text-gray-800">Total Pembayaran</h3>
                    <p class="text-3xl font-black text-blue-700 mt-2">{{ $order->formattedTotalPrice() }}</p>
                </div>

                <!-- Ringkasan -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 space-y-2 text-sm">
                    <p class="font-semibold text-gray-700 mb-2">Ringkasan Pesanan #{{ $order->id }}</p>
                    @foreach ($order->tickets->groupBy('ticket_category_id') as $categoryId => $tickets)
                        @php $cat = $tickets->first()->ticketCategory; @endphp
                        <div class="flex justify-between">
                            <span>{{ $cat->footballMatch->home_team }} vs {{ $cat->footballMatch->away_team }} ({{ $cat->name }}) x{{ $tickets->count() }}</span>
                            <span class="font-medium">Rp {{ number_format($cat->price * $tickets->count(), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="border-t pt-2 flex justify-between font-bold">
                        <span>Total</span>
                        <span>{{ $order->formattedTotalPrice() }}</span>
                    </div>
                </div>

                <!-- Simulasi Payment -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-sm text-yellow-800">
                    ⚠️ <strong>Mode Demo:</strong> Klik tombol di bawah untuk mensimulasikan pembayaran berhasil. Di produksi, integrasikan dengan payment gateway seperti Midtrans atau Xendit.
                </div>

                <form method="POST" action="{{ route('payment.confirm', $order) }}">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-lg hover:bg-green-700 transition-colors">
                        ✅ Konfirmasi Bayar
                    </button>
                </form>

                <a href="{{ route('orders.index') }}" class="block text-center text-gray-500 text-sm mt-4 hover:underline">
                    Bayar nanti
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
