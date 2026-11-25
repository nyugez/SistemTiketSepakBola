<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">⚽ Detail Pertandingan</h2>
    </x-slot>

    <div class="py-8" style="background:#f1f5f9;min-height:80vh;">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Match Hero Card -->
            <div class="rounded-2xl overflow-hidden mb-6 shadow-xl"
                 style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#1e40af 100%);">
                <!-- Stadium badge -->
                <div class="px-6 pt-6 pb-2 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85);backdrop-filter:blur(4px);">
                        🏟️ {{ $match->stadium->name }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85);">
                        📍 {{ $match->stadium->city }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                          style="background:rgba(16,185,129,0.2);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3);">
                        📅 {{ $match->match_date->format('d M Y') }} • {{ $match->match_date->format('H:i') }} WIB
                    </span>
                </div>

                <!-- Teams -->
                <div class="px-6 py-8">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <!-- Home Team -->
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-2xl mx-auto mb-3 flex items-center justify-center text-3xl"
                                 style="background:rgba(255,255,255,0.1);backdrop-filter:blur(4px);">
                                🏠
                            </div>
                            <p class="text-white font-bold text-lg leading-tight break-words">{{ $match->home_team }}</p>
                            <p class="text-xs mt-1 font-medium" style="color:rgba(255,255,255,0.5);">TUAN RUMAH</p>
                        </div>

                        <!-- VS Badge -->
                        <div class="text-center">
                            <div class="inline-block">
                                <p class="font-black text-4xl" style="color:#fbbf24;text-shadow:0 0 20px rgba(251,191,36,0.4);">VS</p>
                            </div>
                        </div>

                        <!-- Away Team -->
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-2xl mx-auto mb-3 flex items-center justify-center text-3xl"
                                 style="background:rgba(255,255,255,0.1);backdrop-filter:blur(4px);">
                                ✈️
                            </div>
                            <p class="text-white font-bold text-lg leading-tight break-words">{{ $match->away_team }}</p>
                            <p class="text-xs mt-1 font-medium" style="color:rgba(255,255,255,0.5);">TAMU</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Categories -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
                <!-- Header -->
                <div class="px-6 py-5" style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
                    <h3 class="text-lg font-bold text-gray-900">🎫 Pilih Kategori Tiket</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Pilih kategori sesuai posisi dan anggaran Anda</p>
                </div>

                <div class="p-6">
                    @if ($match->ticketCategories->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-4xl mb-3">🎟️</p>
                            <p class="text-gray-700 font-semibold">Belum Ada Tiket Tersedia</p>
                            <p class="text-gray-400 text-sm mt-1">Belum ada kategori tiket untuk pertandingan ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($match->ticketCategories as $category)
                                @php
                                    $remaining = $category->remainingQuota();
                                    $isSoldOut = $remaining <= 0;
                                    $isLow = $remaining > 0 && $remaining <= 10;
                                @endphp
                                <div class="relative rounded-xl overflow-hidden transition-all duration-200 {{ $isSoldOut ? 'opacity-60' : 'hover:shadow-md hover:-translate-y-0.5' }}"
                                     style="border:2px solid {{ $isSoldOut ? '#e5e7eb' : '#e0e7ff' }};background:{{ $isSoldOut ? '#f9fafb' : '#ffffff' }};">
                                    <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <!-- Left: Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <h4 class="font-bold text-gray-900 text-base">{{ $category->name }}</h4>
                                                @if ($isSoldOut)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                          style="background:#fee2e2;color:#dc2626;">HABIS</span>
                                                @elseif ($isLow)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                          style="background:#fef3c7;color:#d97706;">HAMPIR HABIS</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                                          style="background:#d1fae5;color:#059669;">TERSEDIA</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <div class="flex items-center gap-1">
                                                    @php $pct = $category->quota > 0 ? (($category->quota - $remaining) / $category->quota) * 100 : 100; @endphp
                                                    <div class="w-20 h-1.5 rounded-full overflow-hidden" style="background:#e5e7eb;">
                                                        <div class="h-full rounded-full transition-all"
                                                             style="width:{{ $pct }}%;background:{{ $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#f59e0b' : '#10b981') }};"></div>
                                                    </div>
                                                </div>
                                                <span class="text-xs font-medium" style="color:{{ $isSoldOut ? '#9ca3af' : '#374151' }};">
                                                    {{ $remaining }} / {{ $category->quota }} sisa
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Right: Price + Action -->
                                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                            <p class="text-2xl font-black" style="color:#1d4ed8;">{{ $category->formattedPrice() }}</p>
                                            <p class="text-xs text-gray-400">per tiket</p>
                                            @auth
                                                @if (!$isSoldOut)
                                                    <a href="{{ route('orders.create', $match) }}?category={{ $category->id }}"
                                                       class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                                                       style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 12px rgba(37,99,235,0.35);">
                                                        🛒 Pesan Sekarang
                                                    </a>
                                                @else
                                                    <span class="px-5 py-2.5 rounded-xl text-sm font-semibold"
                                                          style="background:#f3f4f6;color:#9ca3af;cursor:not-allowed;">
                                                        Tiket Habis
                                                    </span>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}"
                                                   class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
                                                   style="background:#f8fafc;border:2px solid #e2e8f0;color:#374151;">
                                                    🔐 Login untuk Pesan
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6 pt-5" style="border-top:1px solid #f1f5f9;">
                        <a href="{{ route('matches.index') }}"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                            ← Kembali ke Daftar Pertandingan
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

