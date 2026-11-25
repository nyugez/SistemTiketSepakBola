<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">🎟️ Tiket Saya</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($tickets->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-5xl mb-4">🎫</p>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Tiket Aktif</h3>
                    <p class="text-gray-500 mb-6">Anda belum memiliki E-Ticket aktif. Silakan pesan dan bayar tiket pertandingan.</p>
                    <a href="{{ route('matches.index') }}"
                       class="inline-block bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        Cari Pertandingan
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($tickets as $ticket)
                        @php
                            $match = $ticket->ticketCategory?->footballMatch;
                            $stadium = $match?->stadium;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-blue-700 to-blue-900 p-5 text-white">
                                <div class="flex items-center justify-between text-xs text-blue-200 mb-2">
                                    <span>E-TICKET #{{ $ticket->id }}</span>
                                    <span class="bg-green-500/20 text-green-300 border border-green-400/30 px-2 py-0.5 rounded-full font-semibold">LUNAS</span>
                                </div>
                                <h3 class="font-bold text-lg leading-tight">
                                    {{ $match?->home_team }} vs {{ $match?->away_team }}
                                </h3>
                                <p class="text-xs text-blue-100 mt-1">
                                    🏟️ {{ $stadium?->name }}, {{ $stadium?->city }}
                                </p>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 space-y-4 flex-1">
                                <div class="grid grid-cols-2 gap-2 text-xs bg-gray-50 p-3 rounded-xl">
                                    <div>
                                        <p class="text-gray-500">Tanggal</p>
                                        <p class="font-semibold text-gray-800">{{ $match?->match_date?->format('d M Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Waktu</p>
                                        <p class="font-semibold text-gray-800">{{ $match?->match_date?->format('H:i') }} WIB</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-b py-3">
                                    <div>
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-0.5 rounded-md text-xs font-semibold">
                                            {{ $ticket->ticketCategory?->name }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs text-gray-400 block">Kursi</span>
                                        <span class="text-lg font-black text-gray-900 font-mono">{{ $ticket->seat_number }}</span>
                                    </div>
                                </div>

                                <div class="text-center bg-gray-50 p-2.5 rounded-lg">
                                    <p class="text-xs text-gray-500">Kode Unik Tiket:</p>
                                    <p class="font-mono font-bold text-sm text-blue-700 tracking-wider">
                                        {{ $ticket->qr_code ?? 'TKT-' . $ticket->id }}
                                    </p>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="p-5 pt-0 grid grid-cols-2 gap-2">
                                <a href="{{ route('tickets.show', $ticket->id) }}"
                                   class="text-center bg-blue-50 text-blue-700 py-2 rounded-xl text-xs font-semibold hover:bg-blue-100 transition">
                                    Lihat QR Code
                                </a>
                                <a href="{{ route('tickets.download', $ticket->id) }}"
                                   class="text-center bg-red-600 text-white py-2 rounded-xl text-xs font-semibold hover:bg-red-700 transition">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $tickets->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
