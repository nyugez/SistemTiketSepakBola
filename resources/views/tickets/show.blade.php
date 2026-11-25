<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tiket Anda</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <!-- Card Tiket -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">

                <!-- Header Biru -->
                <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white p-6 text-center">
                    <p class="text-xs tracking-widest text-blue-200 mb-2">🏆 TIKET PERTANDINGAN</p>
                    <h3 class="text-xl font-bold">
                        {{ $ticket->ticketCategory->footballMatch->home_team }}
                        <span class="text-blue-300 mx-2 text-sm">VS</span>
                        {{ $ticket->ticketCategory->footballMatch->away_team }}
                    </h3>
                </div>

                <!-- Body Tiket -->
                <div class="p-6">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm mb-5">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">📅 Tanggal</p>
                            <p class="font-semibold">{{ $ticket->ticketCategory->footballMatch->match_date->format('d M Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500 text-xs">🕐 Waktu</p>
                            <p class="font-semibold">{{ $ticket->ticketCategory->footballMatch->match_date->format('H:i') }} WIB</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 col-span-2">
                            <p class="text-gray-500 text-xs">🏟️ Stadion</p>
                            <p class="font-semibold">{{ $ticket->ticketCategory->footballMatch->stadium->name }}, {{ $ticket->ticketCategory->footballMatch->stadium->city }}</p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-2 border-dashed border-gray-200 my-5"></div>

                    <!-- Kategori & Kursi -->
                    <div class="text-center mb-5">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $ticket->ticketCategory->name }}
                        </span>
                        <div class="mt-3">
                            <p class="text-xs text-gray-500">NOMOR KURSI</p>
                            <p class="text-3xl font-black text-blue-700">{{ $ticket->seat_number }}</p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center mb-5">
                        <p class="text-xs text-gray-500 mb-3">Scan QR Code ini di pintu masuk</p>
                        <div class="inline-block border-2 border-blue-200 rounded-xl p-3">
                            @if(class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class))
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate($ticket->qr_code) !!}
                            @else
                                <div class="w-40 h-40 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <p class="text-4xl">📱</p>
                                        <p class="text-xs text-gray-500 mt-1">QR Code</p>
                                        <p class="text-xs font-mono text-gray-700 mt-1">{{ $ticket->qr_code }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-xs font-mono text-gray-500 mt-2">{{ $ticket->qr_code }}</p>
                    </div>

                    <!-- Pemegang -->
                    <div class="bg-gray-50 rounded-lg p-3 text-sm text-center">
                        <p class="text-gray-500 text-xs">Pemegang Tiket</p>
                        <p class="font-bold">{{ $ticket->order->user->name }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 pb-6 space-y-2">
                    <a href="{{ route('tickets.download', $ticket->id) }}"
                        class="block w-full bg-red-600 text-white text-center py-3 rounded-xl font-semibold hover:bg-red-700 transition-colors">
                        📄 Download PDF
                    </a>
                    <div class="flex justify-between text-xs text-blue-600 pt-2">
                        <a href="{{ route('tickets.my') }}" class="hover:underline">
                            ← Daftar Tiket Saya
                        </a>
                        <a href="{{ route('orders.show', $ticket->order_id) }}" class="hover:underline">
                            Lihat Rincian Pesanan →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
