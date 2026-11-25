@extends('layouts.admin')
@section('title', 'Riwayat Sanksi — ' . $user->name)

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.sanctions.index') }}"
           class="p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
            ⬅️ Kembali
        </a>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Rekam Jejak & Riwayat Sanksi Suporter</h3>
            <p class="text-xs text-gray-500 mt-0.5">Audit log pelanggaran aturan dan status keikutsertaan suporter</p>
        </div>
    </div>

    <div>
        @if ($user->isBanned())
            <button type="button"
                    onclick="openRevokeModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->ban_reason) }}')"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-colors flex items-center gap-1.5">
                <span>✅</span> Cabut Sanksi
            </button>
        @else
            <button type="button"
                    onclick="openBanModal('{{ $user->id }}', '{{ addslashes($user->name) }}')"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-colors flex items-center gap-1.5">
                <span>🚫</span> Beri Sanksi / Blacklist
            </button>
        @endif
    </div>
</div>

<!-- Kartu Profil & Status -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Profil Suporter -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-bold text-white text-2xl flex-shrink-0"
                     style="background: {{ $user->isBanned() ? 'linear-gradient(135deg, #ef4444, #b91c1c)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' }};">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-lg leading-tight">{{ $user->name }}</h4>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
                    <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->isBanned() ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $user->isBanned() ? '🚫 Blacklisted' : '✅ Akun Normal' }}
                    </span>
                </div>
            </div>

            <div class="space-y-2 pt-3 border-t border-gray-100 text-xs text-gray-600">
                <div class="flex justify-between py-1">
                    <span class="text-gray-400">ID Pengguna</span>
                    <span class="font-semibold text-gray-800">#{{ $user->id }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-gray-400">Terdaftar Sejak</span>
                    <span class="font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-gray-400">Total Transaksi Tiket</span>
                    <span class="font-semibold text-gray-800">{{ $user->orders->count() }} Pesanan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Status Sanksi Terkini -->
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Status Keamanan Stadion Terkini</span>
                @if ($user->isBanned())
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                        AKSES PEMBELIAN TIKET DIBLOKIR
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                        DAPAT MEMBELI TIKET & MASUK STADION
                    </span>
                @endif
            </div>

            @if ($user->isBanned())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span>⚠️</span> Suporter ini sedang aktif berada dalam daftar Blacklist!
                    </div>
                    <p class="text-xs leading-relaxed">
                        <strong class="font-semibold">Alasan Penindakan:</strong> {{ $user->ban_reason }}
                    </p>
                    <p class="text-xs text-red-700 pt-1 border-t border-red-200/60">
                        📅 Diberlakukan pada: <strong>{{ $user->banned_at?->format('d M Y, H:i') ?? '-' }} WIB</strong>
                    </p>
                </div>
            @else
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span>🛡️</span> Tidak ada sanksi aktif pada suporter ini.
                    </div>
                    <p class="text-xs text-emerald-700">
                        Suporter memiliki reputasi aman dan memiliki hak penuh untuk memilih pertandingan serta membeli tiket di stadion.
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-400 flex items-center justify-between">
            <span>Sistem Proteksi Suporter Sepak Bola</span>
            <span>Total Pelanggaran Dicatat: {{ $user->sanctions->count() }} Kali</span>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Sanksi -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h4 class="font-bold text-gray-900 text-sm">📜 Buku Riwayat Sanksi & Pelanggaran</h4>
            <p class="text-xs text-gray-500 mt-0.5">Daftar rekam jejak indisipliner yang pernah diterima akun ini</p>
        </div>
        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
            {{ $user->sanctions->count() }} Catatan
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe Sanksi</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kronologi & Alasan</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Diberikan Oleh</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status & Info Pencabutan</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Waktu Catat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($user->sanctions as $sanction)
                <tr class="hover:bg-slate-50 transition-colors {{ $sanction->isActive() ? 'bg-red-50/20' : '' }}">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $sanction->sanctionTypeBadgeColor() }}">
                            {{ $sanction->sanctionTypeLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-800 max-w-sm">
                        <p class="font-medium leading-relaxed">{{ $sanction->reason }}</p>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600">
                        <p class="font-semibold text-gray-800">{{ $sanction->bannedBy?->name ?? 'Sistem / Admin' }}</p>
                        <p class="text-gray-400 text-[11px]">{{ $sanction->bannedBy?->email ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @if ($sanction->isActive())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-red-700 bg-red-100 border border-red-200">
                                🔴 Aktif Berlaku
                            </span>
                        @else
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-medium text-emerald-700 bg-emerald-100 border border-emerald-200">
                                    🟢 Telah Dicabut
                                </span>
                                <p class="text-gray-600 text-[11px] mt-1">
                                    <strong>Oleh:</strong> {{ $sanction->revokedBy?->name ?? 'Admin' }}
                                </p>
                                @if($sanction->revocation_reason)
                                    <p class="text-gray-500 text-[11px] italic">
                                        "{{ $sanction->revocation_reason }}"
                                    </p>
                                @endif
                                <p class="text-gray-400 text-[10px]">
                                    {{ $sanction->revoked_at?->format('d M Y H:i') }} WIB
                                </p>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ $sanction->created_at->format('d M Y') }}
                        <span class="block text-gray-400 text-[11px]">{{ $sanction->created_at->format('H:i') }} WIB</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-xs">
                        🎉 Suporter ini belum pernah memiliki rekam jejak pelanggaran atau sanksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Pembelian Tiket Sebelumnya -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100">
        <h4 class="font-bold text-gray-900 text-sm">🎟️ Riwayat Pemesanan Tiket Terakhir</h4>
        <p class="text-xs text-gray-500 mt-0.5">Daftar pertandingan sepak bola yang pernah dipesan oleh suporter ini</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Pertandingan</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Jumlah Tiket</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total Bayar</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status Pembayaran</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($user->orders as $order)
                @php
                    $match = $order->tickets->first()?->ticketCategory?->footballMatch;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-3.5 text-xs font-bold text-gray-700">#{{ $order->id }}</td>
                    <td class="px-6 py-3.5 text-xs text-gray-800 font-medium">
                        {{ $match ? $match->home_team . ' vs ' . $match->away_team : 'Detail tidak ditemukan' }}
                    </td>
                    <td class="px-6 py-3.5 text-xs text-gray-600">{{ $order->tickets->count() }} Tiket</td>
                    <td class="px-6 py-3.5 text-xs font-semibold text-gray-800">{{ $order->formattedTotalPrice() }}</td>
                    <td class="px-6 py-3.5">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $order->paymentStatusColor() }}">
                            {{ $order->paymentStatusLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-6 text-center text-gray-400 text-xs">
                        Suporter belum pernah melakukan pemesanan tiket.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL BERI SANKSI ================= -->
<div id="banModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-fade-in border border-gray-100">
        <div class="bg-red-600 px-6 py-4 flex items-center justify-between text-white">
            <h4 class="font-bold text-base flex items-center gap-2">
                <span>🚫</span> Beri Sanksi / Blacklist Suporter
            </h4>
            <button type="button" onclick="closeBanModal()" class="text-white/80 hover:text-white text-lg font-bold">&times;</button>
        </div>

        <form id="banForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Suporter</label>
                <input type="text" id="banUserName" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 font-semibold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tingkat / Jenis Sanksi <span class="text-red-500">*</span></label>
                <select name="sanction_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none">
                    <option value="blacklist">Blacklist Permanen (Dilarang Membeli Tiket & Masuk Stadion)</option>
                    <option value="suspend">Skorsing Sementara (Pembekuan Akun)</option>
                    <option value="warning">Peringatan Keras Resmi</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Alasan Sanksi & Kronologi Pelanggaran <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="4" required minlength="5"
                          placeholder="Jelaskan kronologi pelanggaran suporter..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeBanModal()" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-md transition-colors">
                    Terapkan Sanksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL CABUT SANKSI ================= -->
<div id="revokeModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-fade-in border border-gray-100">
        <div class="bg-emerald-600 px-6 py-4 flex items-center justify-between text-white">
            <h4 class="font-bold text-base flex items-center gap-2">
                <span>✅</span> Pencabutan Sanksi / Pemulihan Akun
            </h4>
            <button type="button" onclick="closeRevokeModal()" class="text-white/80 hover:text-white text-lg font-bold">&times;</button>
        </div>

        <form id="revokeForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Suporter</label>
                <input type="text" id="revokeUserName" readonly class="w-full bg-gray-100 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 font-semibold cursor-not-allowed">
            </div>

            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-xs font-bold text-red-800">Alasan Pelanggaran Sebelumnya:</p>
                <p id="revokePriorReason" class="text-xs text-red-700 mt-0.5 italic"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Alasan Pencabutan Sanksi <span class="text-red-500">*</span></label>
                <textarea name="revocation_reason" rows="3" required minlength="5"
                          placeholder="Tuliskan pertimbangan pencabutan sanksi..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeRevokeModal()" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition-colors">
                    Konfirmasi Pulihkan Akun
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBanModal(userId, userName) {
        document.getElementById('banUserName').value = userName;
        document.getElementById('banForm').action = "{{ url('admin/sanctions') }}/" + userId + "/ban";
        document.getElementById('banModal').classList.remove('hidden');
    }

    function closeBanModal() {
        document.getElementById('banModal').classList.add('hidden');
    }

    function openRevokeModal(userId, userName, priorReason) {
        document.getElementById('revokeUserName').value = userName;
        document.getElementById('revokePriorReason').innerText = priorReason || 'Tidak ada keterangan.';
        document.getElementById('revokeForm').action = "{{ url('admin/sanctions') }}/" + userId + "/revoke";
        document.getElementById('revokeModal').classList.remove('hidden');
    }

    function closeRevokeModal() {
        document.getElementById('revokeModal').classList.add('hidden');
    }

    window.addEventListener('click', function(e) {
        const banModal = document.getElementById('banModal');
        const revokeModal = document.getElementById('revokeModal');
        if (e.target === banModal) closeBanModal();
        if (e.target === revokeModal) closeRevokeModal();
    });
</script>
@endsection
