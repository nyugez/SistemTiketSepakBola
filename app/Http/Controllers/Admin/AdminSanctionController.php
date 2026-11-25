<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSanctionController extends Controller
{
    /**
     * Menampilkan daftar suporter/customer beserta status sanksi dan filternya
     */
    public function index(Request $request)
    {
        // Query khusus user/suporter (non-admin)
        $query = User::where('role', 'user')->with(['latestActiveSanction'])->latest();

        // Filter status sanksi: banned / active
        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        // Pencarian berdasarkan nama atau email suporter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        // Data statistik untuk ringkasan di dashboard sanksi
        $stats = [
            'total_users'     => User::where('role', 'user')->count(),
            'banned_users'    => User::where('role', 'user')->where('is_banned', true)->count(),
            'active_users'    => User::where('role', 'user')->where('is_banned', false)->count(),
            'total_sanctions' => UserSanction::count(),
        ];

        return view('admin.sanctions.index', compact('users', 'stats'));
    }

    /**
     * Menampilkan profil suporter beserta riwayat lengkap sanksi/pelanggaran dan pesanannya
     */
    public function show(User $user)
    {
        // Pastikan relasi riwayat sanksi dan pemesanan di-load
        $user->load([
            'sanctions.bannedBy',
            'sanctions.revokedBy',
            'orders.tickets.ticketCategory.footballMatch.stadium'
        ]);

        return view('admin.sanctions.show', compact('user'));
    }

    /**
     * Memberikan sanksi (Blacklist/Suspend/Warning) kepada suporter
     */
    public function ban(Request $request, User $user)
    {
        // Cegah pemberian sanksi kepada sesama Administrator
        if ($user->isAdmin()) {
            return back()->with('error', 'Tindakan ditolak: Tidak dapat memberikan sanksi kepada akun Administrator.');
        }

        $request->validate([
            'sanction_type' => 'required|in:blacklist,suspend,warning',
            'reason'        => 'required|string|min:5|max:1000',
        ], [
            'reason.required' => 'Alasan sanksi / kronologi pelanggaran wajib diisi.',
            'reason.min'      => 'Alasan sanksi minimal terdiri dari 5 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan riwayat sanksi baru
            UserSanction::create([
                'user_id'       => $user->id,
                'sanction_type' => $request->sanction_type,
                'reason'        => $request->reason,
                'status'        => 'active',
                'banned_by'     => Auth::id(),
            ]);

            // 2. Perbarui status user menjadi banned jika jenis sanksi adalah blacklist atau suspend
            $user->update([
                'is_banned'   => true,
                'ban_reason'  => $request->reason,
                'banned_at'   => now(),
            ]);

            DB::commit();

            return back()->with('success', "Sanksi berhasil diberikan kepada suporter {$user->name}. Akun telah masuk daftar blacklist.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memberikan sanksi: ' . $e->getMessage());
        }
    }

    /**
     * Mencabut sanksi (Unban / Pulihkan Akun) suporter
     */
    public function revoke(Request $request, User $user)
    {
        $request->validate([
            'revocation_reason' => 'required|string|min:5|max:1000',
        ], [
            'revocation_reason.required' => 'Alasan pencabutan sanksi wajib diisi.',
            'revocation_reason.min'      => 'Alasan pencabutan sanksi minimal terdiri dari 5 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update semua rekam jejak sanksi aktif suporter menjadi dicabut (revoked)
            UserSanction::where('user_id', $user->id)
                ->where('status', 'active')
                ->update([
                    'status'            => 'revoked',
                    'revoked_at'        => now(),
                    'revoked_by'        => Auth::id(),
                    'revocation_reason' => $request->revocation_reason,
                ]);

            // 2. Pulihkan status user
            $user->update([
                'is_banned'   => false,
                'ban_reason'  => null,
                'banned_at'   => null,
            ]);

            DB::commit();

            return back()->with('success', "Sanksi untuk suporter {$user->name} berhasil dicabut. Akun kembali aktif dan dapat membeli tiket.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencabut sanksi: ' . $e->getMessage());
        }
    }
}
