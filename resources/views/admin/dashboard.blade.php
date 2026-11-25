@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<!-- Kartu Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <p class="text-xs font-semibold text-gray-400 uppercase">Total Pengguna</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_users']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-xs font-semibold text-gray-400 uppercase">Total Pertandingan</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_matches']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-xs font-semibold text-gray-400 uppercase">Total Pesanan</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_orders']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
        <p class="text-xs font-semibold text-gray-400 uppercase">Total Pendapatan</p>
        <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
    </div>
    <a href="{{ route('admin.sanctions.index') }}" class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500 hover:bg-red-50/50 transition-colors block">
        <div class="flex justify-between items-center">
            <p class="text-xs font-semibold text-red-500 uppercase">Blacklist Suporter</p>
            <span class="text-sm">🚫</span>
        </div>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['banned_users']) }}</p>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800">Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-blue-600 text-sm hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3">
            @forelse ($latestOrders as $order)
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <div>
                    <p class="font-medium text-sm">{{ $order->user?->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold">{{ $order->formattedTotalPrice() }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $order->paymentStatusColor() }}">
                        {{ $order->paymentStatusLabel() }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada pesanan.</p>
            @endforelse
        </div>
    </div>

    <!-- Pertandingan Mendatang -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800">Pertandingan Mendatang</h3>
            <a href="{{ route('admin.matches.index') }}" class="text-blue-600 text-sm hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3">
            @forelse ($upcomingMatches as $match)
            <div class="py-2 border-b border-gray-100">
                <p class="font-medium text-sm">{{ $match->home_team }} vs {{ $match->away_team }}</p>
                <p class="text-xs text-gray-500">{{ $match->stadium?->name }} • {{ $match->match_date?->format('d M Y H:i') }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Belum ada pertandingan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
