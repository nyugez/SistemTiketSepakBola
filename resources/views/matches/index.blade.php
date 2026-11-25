<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">⚽ Jadwal Pertandingan</h2>
        <p class="text-sm mt-0.5" style="color:rgba(255,255,255,0.65);">RVP Ticket Ball — Temukan pertandingan dan pesan tiket Anda</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($matches->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-5xl mb-4">⚽</p>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Pertandingan</h3>
                    <p class="text-gray-500">Saat ini belum ada jadwal pertandingan yang tersedia.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($matches as $match)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-blue-700 to-blue-900 p-5 text-white">
                                <div class="flex items-center justify-between text-xs text-blue-200 mb-2">
                                    <span>PERTANDINGAN</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $match->statusColor() }}">
                                        {{ $match->statusLabel() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2 mt-2">
                                    <span class="font-bold text-base flex-1">{{ $match->home_team }}</span>
                                    <span class="text-yellow-400 font-black text-xs px-2 py-1 bg-blue-800/60 rounded">VS</span>
                                    <span class="font-bold text-base flex-1 text-right">{{ $match->away_team }}</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 space-y-3 flex-1">
                                <div class="space-y-2 text-sm">
                                    <p class="flex items-center gap-2 font-medium text-gray-800">
                                        <span>🏟️</span>
                                        <span>{{ $match->stadium?->name }}, {{ $match->stadium?->city }}</span>
                                    </p>
                                    <p class="flex items-center gap-2 font-medium text-gray-800">
                                        <span>📅</span>
                                        <span>{{ $match->match_date?->format('d M Y') }}</span>
                                    </p>
                                    <p class="flex items-center gap-2 font-medium text-gray-800">
                                        <span>🕐</span>
                                        <span>{{ $match->match_date?->format('H:i') }} WIB</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="p-5 pt-0">
                                <a href="{{ route('matches.show', $match->id) }}"
                                   class="block text-center w-full bg-blue-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                                    Lihat Detail & Pesan Tiket
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $matches->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
