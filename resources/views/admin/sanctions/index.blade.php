@extends('layouts.admin')
@section('title', 'Manajemen Sanksi & Blacklist Suporter')

@section('content')
<!-- Header & Deskripsi Fitur -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            <span>🚫</span> Sistem Manajemen Sanksi & Blacklist Suporter
        </h3>
        <p class="text-sm text-gray-500 mt-1">
            Filter dan tindak tegas suporter bermasalah (kerusuhan, flare, perusakan stadion) untuk menjaga ketertiban, keamanan pertandingan, dan keselamatan penonton.
        </p>
    </div>
</div>

<!-- Statistik Ringkas -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-blue-50 text-blue-600">
            👥
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Suporter</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-red-50 text-red-600">
            🚫
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Aktif Blacklist</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['banned_users']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-emerald-50 text-emerald-600">
            ✅
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bebas Sanksi</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['active_users']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-amber-50 text-amber-600">
            📑
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Histori Kasus</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sanctions']) }}</p>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
    <form method="GET" action="{{ route('admin.sanctions.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Suporter</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Ketik nama atau email suporter..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status Sanksi</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>🚫 Terkena Sanksi (Banned)</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ Bebas Sanksi (Normal)</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                🔍 Filter
            </button>
            <a href="{{ route('admin.sanctions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition-colors">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Data Suporter & Sanksi -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Suporter</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Akses Tiket</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alasan Terakhir / Catatan</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu Sanksi</th>
                    <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                <tr class="hover:bg-slate-50 transition-colors {{ $user->isBanned() ? 'bg-red-50/30' : '' }}">
                    <!-- Info Suporter -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                                 style="background: {{ $user->isBanned() ? 'linear-gradient(135deg, #ef4444, #b91c1c)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' }};">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm flex items-center gap-1.5">
                                    {{ $user->name }}
                                    @if($user->isBanned())
                                        <span class="text-xs px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-medium">Banned</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    <!-- Status Sanksi -->
                    <td class="px-6 py-4">
                        @if ($user->isBanned())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                Blacklist Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Bebas Sanksi
                            </span>
                        @endif
                    </td>

                    <!-- Alasan / Catatan Sanksi -->
                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                        @if ($user->isBanned())
                            <p class="font-medium text-red-900 text-xs line-clamp-2" title="{{ $user->ban_reason }}">
                                ⚠️ {{ $user->ban_reason }}
                            </p>
                        @else
                            <span class="text-gray-400 text-xs italic">Tidak ada catatan pelanggaran aktif.</span>
                        @endif
                    </td>

                    <!-- Waktu Sanksi -->
                    <td class="px-6 py-4 text-xs text-gray-500">
                        @if ($user->banned_at)
                            <span class="font-medium text-gray-800">{{ $user->banned_at->format('d M Y') }}</span>
                            <span class="block text-gray-400">{{ $user->banned_at->format('H:i') }} WIB</span>
                        @else
                            -
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <!-- Tombol Riwayat Lengkap -->
                            <a href="{{ route('admin.sanctions.show', $user) }}"
                               class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors">
                                📑 Riwayat
                            </a>

                            @if ($user->isBanned())
                                <!-- Tombol Cabut Sanksi -->
                                <button type="button"
                                        onclick="openRevokeModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->ban_reason) }}')"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-sm">
                                    ✅ Cabut Sanksi
                                </button>
                            @else
                                <!-- Tombol Beri Sanksi -->
                                <button type="button"
                                        onclick="openBanModal('{{ $user->id }}', '{{ addslashes($user->name) }}')"
                                        class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors shadow-sm">
                                    🚫 Beri Sanksi
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <p class="text-3xl mb-2">⚽</p>
                        <p class="font-medium">Data suporter tidak ditemukan.</p>
                        <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter status.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
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
                          placeholder="Jelaskan kronologi pelanggaran suporter (misal: Menyalakan flare di Tribun Selatan laga Derby, merusak fasilitas kursi stadion, perkelahian penonton, tiket palsu)..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:outline-none"></textarea>
                <p class="text-xs text-gray-400 mt-1">Alasan ini akan tercatat dalam buku audit pelanggaran keamanan dan ditampilkan saat penolakan tiket.</p>
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
                          placeholder="Tuliskan pertimbangan pencabutan sanksi (misal: Masa skorsing 6 bulan telah berakhir, mediasi dan surat pernyataan damai telah ditandatangani, verifikasi ulang identitas suporter)..."
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

    // Tutup modal jika klik di luar box
    window.addEventListener('click', function(e) {
        const banModal = document.getElementById('banModal');
        const revokeModal = document.getElementById('revokeModal');
        if (e.target === banModal) closeBanModal();
        if (e.target === revokeModal) closeRevokeModal();
    });
</script>
@endsection
