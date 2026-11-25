@extends('layouts.admin')
@section('title', 'Kelola Kategori Tiket')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
    <div class="flex justify-between items-center px-6 py-5" style="border-bottom:1px solid #f1f5f9;">
        <div>
            <h3 class="font-bold text-gray-900">🎫 Daftar Kategori Tiket</h3>
            <p class="text-xs text-gray-500 mt-0.5">Atur harga dan kuota tiket untuk setiap pertandingan</p>
        </div>
        <a href="{{ route('admin.ticket-categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-all"
           style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3);">
            + Tambah Kategori
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pertandingan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kuota</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr class="hover:bg-blue-50 transition-colors" style="border-top:1px solid #f1f5f9;">
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $categories->firstItem() + $loop->index }}</td>
                    <td class="px-6 py-4">
                        @if ($category->footballMatch)
                            <p class="font-semibold text-gray-900 text-sm">
                                {{ $category->footballMatch->home_team }} vs {{ $category->footballMatch->away_team }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $category->footballMatch->match_date?->format('d M Y H:i') }}
                            </p>
                        @else
                            <span class="text-xs text-gray-400 italic">Pertandingan sudah dihapus</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $category->formattedPrice() }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                              style="background:#eff6ff;color:#2563eb;">
                            {{ $category->remainingQuota() }} / {{ $category->quota }} tersisa
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.ticket-categories.edit', $category) }}"
                               class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">Edit</a>
                            <form method="POST" action="{{ route('admin.ticket-categories.destroy', $category) }}"
                                  onsubmit="return confirm('Yakin hapus kategori {{ addslashes($category->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-3xl mb-2">🎫</p>
                        <p class="text-gray-500 font-medium">Belum ada kategori tiket.</p>
                        <a href="{{ route('admin.ticket-categories.create') }}" class="text-blue-600 text-sm hover:underline mt-1 inline-block">Tambah kategori pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->hasPages())
    <div class="px-6 py-4" style="border-top:1px solid #f1f5f9;">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
