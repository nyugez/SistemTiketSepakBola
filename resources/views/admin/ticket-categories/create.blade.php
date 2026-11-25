@extends('layouts.admin')
@section('title', 'Tambah Kategori Tiket')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-5" style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <h3 class="font-bold text-gray-900">🎫 Tambah Kategori Tiket</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tentukan harga dan kuota untuk pertandingan</p>
        </div>

        <form method="POST" action="{{ route('admin.ticket-categories.store') }}" class="p-6 space-y-5">
            @csrf
            @include('admin.ticket-categories._form')

            <div class="flex items-center gap-3 pt-2" style="border-top:1px solid #f1f5f9;">
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white transition-all"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3);">
                    💾 Simpan Kategori
                </button>
                <a href="{{ route('admin.ticket-categories.index') }}"
                   class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-100"
                   style="border:1px solid #e2e8f0;">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
