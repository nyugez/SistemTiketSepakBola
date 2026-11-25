<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">📋 Riwayat Pesanan Saya</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            @if ($orders->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-5xl mb-4">🎟️</p>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 mb-6">Anda belum pernah melakukan pemesanan tiket pertandingan.</p>
                    <a href="{{ route('matches.index') }}"
                       class="inline-block bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        Lihat Pertandingan Tersedia
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-gray-900 text-base">Pesanan #{{ $order->id }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $order->paymentStatusColor() }}">
                                            {{ $order->paymentStatusLabel() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 font-medium mt-1">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>

                                <div class="text-left md:text-right">
                                    <p class="text-xs text-gray-500 font-medium">Total Tagihan</p>
                                    <p class="text-xl font-bold text-blue-700">{{ $order->formattedTotalPrice() }}</p>
                                </div>
                            </div>

                            <!-- Ringkasan Tiket -->
                            <div class="space-y-2 mb-4">
                                @forelse ($order->tickets->groupBy('ticket_category_id') as $catId => $tickets)
                                    @php $cat = $tickets->first()->ticketCategory; @endphp
                                    <div class="text-sm text-gray-700 flex flex-wrap justify-between items-center">
                                        <div>
                                            ⚽ <span class="font-semibold">{{ $cat?->footballMatch?->home_team ?? 'Pertandingan' }} vs {{ $cat?->footballMatch?->away_team ?? '' }}</span>
                                            <span class="text-gray-500">({{ $cat?->name ?? 'Tiket' }})</span>
                                        </div>
                                        <div class="text-gray-600 font-medium">
                                            {{ $tickets->count() }} Tiket
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-400 italic">Tidak ada tiket aktif pada pesanan ini.</p>
                                @endforelse
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t">
                                <a href="{{ route('orders.show', $order->id) }}"
                                   class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                                    Lihat Rincian Pesanan →
                                </a>

                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($order->payment_status === 'pending')
                                        <!-- Tombol Batalkan Pesanan -->
                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan #{{ $order->id }}? Kuota tiket akan dikembalikan.');">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-gray-100 text-gray-700 px-3.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-50 hover:text-red-700 transition">
                                                Batalkan
                                            </button>
                                        </form>

                                        <!-- Tombol Bayar -->
                                        <a href="{{ route('payment.show', $order) }}"
                                           class="bg-green-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-green-700 transition">
                                            Bayar Sekarang
                                        </a>
                                    @elseif ($order->payment_status === 'paid')
                                        <a href="{{ route('tickets.my') }}"
                                           class="bg-blue-600 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-700 transition">
                                            Lihat Tiket
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
