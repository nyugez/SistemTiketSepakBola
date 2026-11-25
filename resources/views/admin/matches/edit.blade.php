@extends('layouts.admin')
@section('title', 'Edit Pertandingan')

@section('content')
<div class="max-w-2xl bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-6">Edit Pertandingan</h3>
    <form method="POST" action="{{ route('admin.matches.update', $match) }}" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tim Tuan Rumah</label>
                <input type="text" name="home_team" value="{{ old('home_team', $match->home_team) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @error('home_team')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tim Tamu</label>
                <input type="text" name="away_team" value="{{ old('away_team', $match->away_team) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @error('away_team')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stadion</label>
            <select name="stadium_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @foreach ($stadiums as $stadium)
                    <option value="{{ $stadium->id }}" {{ old('stadium_id', $match->stadium_id) == $stadium->id ? 'selected' : '' }}>
                        {{ $stadium->name }} ({{ $stadium->city }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu</label>
            <input type="datetime-local" name="match_date"
                value="{{ old('match_date', $match->match_date->format('Y-m-d\TH:i')) }}" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @foreach (['scheduled' => 'Dijadwalkan', 'ongoing' => 'Sedang Berlangsung', 'finished' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $match->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button>
            <a href="{{ route('admin.matches.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">Batal</a>
        </div>
    </form>
</div>
@endsection
