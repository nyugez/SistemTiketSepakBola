@extends('layouts.admin')
@section('title', 'Kelola Pesanan')

@section('content')
<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Cari nama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama pengguna..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">ID</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Pengguna</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Total</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Status</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Tanggal</th>
                <th class="text-left px-6 py-3 text-xs text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500">#{{ $order->id }}</td>
                <td class="px-6 py-4 font-medium">{{ $order->user?->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $order->formattedTotalPrice() }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs {{ $order->paymentStatusColor() }}">
                        {{ $order->paymentStatusLabel() }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500 text-sm">{{ $order->created_at->format('d M Y') }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
