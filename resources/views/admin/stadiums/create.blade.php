@extends('layouts.admin')
@section('title', 'Tambah Stadion')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-5" style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <h3 class="font-bold text-gray-900">🏟️ Tambah Stadion Baru</h3>
            <p class="text-xs text-gray-500 mt-0.5">Isi data stadion dengan lengkap dan benar</p>
        </div>

        <form method="POST" action="{{ route('admin.stadiums.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Stadion <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="cth. Stadion Gelora Bung Karno"
                       class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       style="border-color:{{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kota <span class="text-red-500">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}" required
                       placeholder="cth. Jakarta"
                       class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       style="border-color:{{ $errors->has('city') ? '#ef4444' : '#d1d5db' }};">
                @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="address" rows="3" required
                          placeholder="Alamat lengkap stadion..."
                          class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          style="border-color:{{ $errors->has('address') ? '#ef4444' : '#d1d5db' }};">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kapasitas (kursi) <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity') }}" required min="100"
                       placeholder="cth. 80000"
                       class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       style="border-color:{{ $errors->has('capacity') ? '#ef4444' : '#d1d5db' }};">
                @error('capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid #f1f5f9;">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-all"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3);">
                    💾 Simpan Stadion
                </button>
                <a href="{{ route('admin.stadiums.index') }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-100"
                   style="border:1px solid #e2e8f0;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
