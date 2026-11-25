<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">📋 Detail Pesanan #{{ $order->id }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-300 text-red-800 rounded-lg p-4 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6" style="border:1px solid #e2e8f0;">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Nomor Pesanan</p>
                        <p class="font-bold text-gray-900 mt-1">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Status</p>
                        <span class="inline-block mt-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->paymentStatusColor() }}">
                            {{ $order->paymentStatusLabel() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Total Biaya</p>
                        <p class="font-bold text-blue-700 text-lg mt-1">{{ $order->formattedTotalPrice() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Tanggal Pesan</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $order->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                @if ($order->payment_status === 'pending')
                    <div class="mt-6 pt-6 border-t flex flex-wrap gap-3 items-center justify-between">
                        <div class="text-sm text-yellow-700 bg-yellow-50 px-3 py-2 rounded-lg">
                            ⏳ Pesanan menunggu pembayaran Anda.
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok tiket akan dikembalikan.');">
                                @csrf
                                <button type="submit"
                                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-100 hover:text-red-700 transition">
                                    Batalkan Pesanan
                                </button>
                            </form>
                            <a href="{{ route('payment.show', $order) }}"
                               class="bg-green-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                                Bayar Sekarang
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tiket-tiket dalam Order -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-lg text-gray-900 mb-4">Tiket Anda ({{ $order->tickets->count() }})</h3>

                @if ($order->tickets->isEmpty())
                    <p class="text-gray-500 text-sm italic">Tidak ada tiket aktif pada pesanan ini (tiket telah dibatalkan atau dihapus).</p>
                @else
                    <div class="space-y-3">
                        @foreach ($order->tickets as $ticket)
                        <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                        <div>
                                <p class="font-bold text-gray-900">
                                    {{ $ticket->ticketCategory->footballMatch->home_team }} vs {{ $ticket->ticketCategory->footballMatch->away_team }}
                                </p>
                                <p class="text-sm text-gray-700 mt-1">
                                    <span class="font-semibold text-blue-600">{{ $ticket->ticketCategory->name }}</span>
                                    • Kursi: <span class="font-mono font-bold text-gray-900">{{ $ticket->seat_number }}</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">
                                    🏟️ {{ $ticket->ticketCategory->footballMatch->stadium->name }} • 📅 {{ $ticket->ticketCategory->footballMatch->match_date->format('d M Y H:i') }} WIB
                                </p>
                            </div>

                            @if ($order->payment_status === 'paid')
                                <div class="flex gap-2">
                                    <a href="{{ route('tickets.show', $ticket->id) }}"
                                       class="bg-blue-100 text-blue-700 px-3.5 py-1.5 rounded-lg text-sm font-medium hover:bg-blue-200 transition">
                                        Lihat E-Ticket
                                    </a>
                                    <a href="{{ route('tickets.download', $ticket->id) }}"
                                       class="bg-red-600 text-white px-3.5 py-1.5 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                        Download PDF
                                    </a>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">E-Ticket aktif setelah pembayaran lunas</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <a href="{{ route('orders.index') }}" class="text-blue-600 text-sm hover:underline">
                    ← Kembali ke daftar riwayat pesanan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
