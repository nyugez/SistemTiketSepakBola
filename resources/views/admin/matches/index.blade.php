@extends('layouts.admin')
@section('title', 'Kelola Pertandingan')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex justify-between items-center p-6 border-b">
        <h3 class="font-semibold text-gray-800">Daftar Pertandingan</h3>
        <a href="{{ route('admin.matches.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + Tambah Pertandingan
        </a>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Pertandingan</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Stadion</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Tanggal</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Status</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($matches as $match)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $match->home_team }} vs {{ $match->away_team }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $match->stadium?->name ?? '-' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $match->match_date?->format('d M Y H:i') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs {{ $match->statusColor() }}">
                        {{ $match->statusLabel() }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.matches.edit', $match) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Yakin hapus pertandingan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada pertandingan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $matches->links() }}</div>
</div>
@endsection
