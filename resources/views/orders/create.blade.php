<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg text-white">🛋️ Buat Pesanan Tiket</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <!-- Info Pertandingan -->
                <div class="rounded-xl p-5 mb-6" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);">
                    <p class="font-bold text-white text-base">{{ $match->home_team }} <span style="color:#fbbf24;">vs</span> {{ $match->away_team }}</p>
                    <p class="text-sm mt-1.5 font-medium" style="color:rgba(255,255,255,0.7);">🏟️ {{ $match->stadium->name }} &nbsp;•&nbsp; 📅 {{ $match->match_date->format('d M Y, H:i') }} WIB</p>
                </div>

                <form method="POST" action="{{ route('orders.store', $match) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Tiket</label>
                        <select name="ticket_category_id" id="ticket_category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($match->ticketCategories as $category)
                                @if ($category->remainingQuota() > 0)
                                <option value="{{ $category->id }}"
                                    data-price="{{ $category->price }}"
                                    {{ (request('category') == $category->id || old('ticket_category_id') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }} — {{ $category->formattedPrice() }} (Sisa: {{ $category->remainingQuota() }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                        @error('ticket_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tiket</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}"
                            min="1" max="10" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                        @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Total -->
                    <div class="rounded-xl p-5" style="background:#f8fafc;border:2px solid #e0e7ff;">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-700">Total Pembayaran:</span>
                            <span class="text-2xl font-black" style="color:#1d4ed8;" id="total">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700">
                        Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function updateTotal() {
        const sel = document.getElementById('ticket_category_id');
        const qty = parseInt(document.getElementById('quantity').value) || 0;
        const price = sel.options[sel.selectedIndex]?.dataset.price || 0;
        const total = price * qty;
        document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    document.getElementById('ticket_category_id').addEventListener('change', updateTotal);
    document.getElementById('quantity').addEventListener('input', updateTotal);
    updateTotal();
    </script>
</x-app-layout>
