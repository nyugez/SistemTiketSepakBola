@extends('layouts.admin')
@section('title', 'Kelola Stadion')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
    <div class="flex justify-between items-center px-6 py-5" style="border-bottom:1px solid #f1f5f9;">
        <div>
            <h3 class="font-bold text-gray-900">🏟️ Daftar Stadion</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data stadion untuk pertandingan</p>
        </div>
        <a href="{{ route('admin.stadiums.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all"
           style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3);">
            + Tambah Stadion
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Stadion</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kota</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kapasitas</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pertandingan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody style="divide-color:#f1f5f9;">
                @forelse ($stadiums as $stadium)
                <tr class="hover:bg-blue-50 transition-colors" style="border-top:1px solid #f1f5f9;">
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $stadium->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($stadium->address, 40) }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">📍 {{ $stadium->city }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ number_format($stadium->capacity) }} kursi</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                              style="background:#eff6ff;color:#2563eb;">
                            {{ $stadium->football_matches_count }} pertandingan
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.stadiums.edit', $stadium) }}"
                               class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">Edit</a>
                            <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium) }}"
                                  onsubmit="return confirm('Yakin hapus stadion {{ addslashes($stadium->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-3xl mb-2">🏟️</p>
                        <p class="text-gray-500 font-medium">Belum ada stadion.</p>
                        <a href="{{ route('admin.stadiums.create') }}" class="text-blue-600 text-sm hover:underline mt-1 inline-block">Tambah stadion pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($stadiums->hasPages())
    <div class="px-6 py-4" style="border-top:1px solid #f1f5f9;">{{ $stadiums->links() }}</div>
    @endif
</div>
@endsection
