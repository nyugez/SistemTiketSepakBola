@php $ticketCategory = $ticketCategory ?? null; @endphp

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pertandingan <span class="text-red-500">*</span></label>
    <select name="football_match_id" required
            class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            style="border-color:{{ $errors->has('football_match_id') ? '#ef4444' : '#d1d5db' }};">
        <option value="">-- Pilih Pertandingan --</option>
        @foreach ($matches as $match)
            <option value="{{ $match->id }}"
                {{ old('football_match_id', $ticketCategory?->football_match_id) == $match->id ? 'selected' : '' }}>
                {{ $match->home_team }} vs {{ $match->away_team }} — {{ $match->match_date?->format('d M Y H:i') }}
            </option>
        @endforeach
    </select>
    @error('football_match_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    @if ($matches->isEmpty())
        <p class="text-amber-600 text-xs mt-1">Belum ada pertandingan terjadwal. Tambahkan pertandingan terlebih dahulu.</p>
    @endif
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $ticketCategory?->name) }}" required
           placeholder="cth. VIP, Tribun Utara, Ekonomi"
           class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
           style="border-color:{{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};">
    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
        <input type="number" name="price" min="0" step="1" required
               value="{{ old('price', $ticketCategory ? (int) $ticketCategory->price : null) }}"
               placeholder="cth. 150000"
               class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               style="border-color:{{ $errors->has('price') ? '#ef4444' : '#d1d5db' }};">
        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kuota <span class="text-red-500">*</span></label>
        <input type="number" name="quota" min="1" required
               value="{{ old('quota', $ticketCategory?->quota) }}"
               placeholder="cth. 5000"
               class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               style="border-color:{{ $errors->has('quota') ? '#ef4444' : '#d1d5db' }};">
        @error('quota')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
