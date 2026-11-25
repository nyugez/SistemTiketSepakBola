@extends('layouts.admin')
@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke daftar pesanan</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Ringkasan pesanan --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6" style="border:1px solid #e2e8f0;">
            <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">ID Pesanan</dt><dd class="font-semibold">#{{ $order->id }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Pengguna</dt><dd class="font-semibold">{{ $order->user?->name ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="font-semibold">{{ $order->user?->email ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Tanggal</dt><dd class="font-semibold">{{ $order->created_at?->format('d M Y H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Jumlah Tiket</dt><dd class="font-semibold">{{ $order->tickets->count() }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Total</dt><dd class="font-bold">{{ $order->formattedTotalPrice() }}</dd></div>
                <div class="flex justify-between items-center">
                    <dt class="text-gray-500">Status</dt>
                    <dd><span class="px-2 py-1 rounded-full text-xs {{ $order->paymentStatusColor() }}">{{ $order->paymentStatusLabel() }}</span></dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6" style="border:1px solid #e2e8f0;">
            <h3 class="font-bold text-gray-900 mb-4">Ubah Status Pembayaran</h3>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach (['pending' => 'Menunggu Pembayaran', 'paid' => 'Lunas', 'failed' => 'Gagal', 'expired' => 'Kadaluarsa'] as $val => $label)
                        <option value="{{ $val }}" {{ $order->payment_status === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                    Simpan Status
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar tiket --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #e2e8f0;">
        <div class="px-6 py-5" style="border-bottom:1px solid #f1f5f9;">
            <h3 class="font-bold text-gray-900">Tiket dalam Pesanan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Pertandingan</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kursi</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kode QR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->tickets as $ticket)
                    @php $match = $ticket->ticketCategory?->footballMatch; @endphp
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td class="px-6 py-4 text-sm">
                            @if ($match)
                                <p class="font-semibold text-gray-900">{{ $match->home_team }} vs {{ $match->away_team }}</p>
                                <p class="text-xs text-gray-500">{{ $match->stadium?->name }} • {{ $match->match_date?->format('d M Y H:i') }}</p>
                            @else
                                <span class="text-gray-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $ticket->ticketCategory?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $ticket->seat_number }}</td>
                        <td class="px-6 py-4 text-xs font-mono text-gray-600">{{ $ticket->qr_code ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada tiket pada pesanan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
