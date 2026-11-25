<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar riwayat pesanan (orders) milik pengguna yang sedang login
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with([
            'tickets.ticketCategory.footballMatch.stadium'
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menampilkan formulir pemilihan kategori tiket untuk pertandingan tertentu
     */
    public function create(FootballMatch $match)
    {
        // Validasi status sanksi / blacklist suporter
        if (Auth::user()->isBanned()) {
            return redirect()->route('matches.show', $match)
                ->with('error', 'Akses Ditolak: Akun Anda sedang terkena sanksi/blacklist dan tidak diizinkan memesan tiket. Alasan: ' . (Auth::user()->ban_reason ?? 'Pelanggaran aturan suporter/stadion.'));
        }

        $match->load(['stadium', 'ticketCategories']);
        return view('orders.create', compact('match'));
    }

    /**
     * Menyimpan data pesanan baru dan mereservasi tiket
     */
    public function store(Request $request, FootballMatch $match)
    {
        // Validasi status sanksi / blacklist suporter
        if (Auth::user()->isBanned()) {
            return redirect()->route('matches.show', $match)
                ->with('error', 'Akses Ditolak: Akun Anda dalam status blacklist/sanksi suporter dan dilarang membeli tiket. Alasan: ' . (Auth::user()->ban_reason ?? 'Pelanggaran aturan suporter/stadion.'));
        }

        $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity'           => 'required|integer|min:1|max:10',
        ]);

        $category = TicketCategory::findOrFail($request->ticket_category_id);

        // Cek ketersediaan kuota tiket
        if ($category->remainingQuota() < $request->quantity) {
            return back()->withErrors([
                'quantity' => 'Kuota tiket tidak mencukupi. Sisa tiket tersedia: ' . $category->remainingQuota()
            ]);
        }

        DB::beginTransaction();
        try {
            $totalPrice = $category->price * $request->quantity;

            // Buat record order baru dengan status 'pending'
            $order = Order::create([
                'user_id'        => Auth::id(),
                'total_price'    => $totalPrice,
                'payment_status' => Order::PAYMENT_PENDING,
            ]);

            // Buat tiket satu per satu untuk reservasi kursi
            for ($i = 1; $i <= $request->quantity; $i++) {
                Ticket::create([
                    'order_id'           => $order->id,
                    'ticket_category_id' => $category->id,
                    'seat_number'        => $category->name . '-' . str_pad($order->id . $i, 4, '0', STR_PAD_LEFT),
                    'qr_code'            => null, // Diisi setelah pembayaran dikonfirmasi
                ]);
            }

            DB::commit();

            return redirect()->route('payment.show', $order)
                ->with('success', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan detail satu pesanan
     */
    public function show($id)
    {
        $order = Order::with(['tickets.ticketCategory.footballMatch.stadium', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Membatalkan pesanan yang statusnya masih 'pending' dan mengembalikan stok tiket ke database
     *
     * @param int|string $id ID pesanan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->findOrFail($id);

        // Validasi: hanya pesanan berstatus 'pending' yang dapat dibatalkan
        if ($order->payment_status !== Order::PAYMENT_PENDING && $order->payment_status !== 'pending') {
            return redirect()->route('orders.index')
                ->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses atau dibatalkan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Hapus alokasi tiket pada pesanan ini untuk mengembalikan stok kuota
            $order->tickets()->delete();

            // Ubah status pesanan menjadi 'failed' / dibatalkan
            $order->update([
                'payment_status' => Order::PAYMENT_FAILED,
            ]);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Pesanan #' . $order->id . ' berhasil dibatalkan dan kuota tiket telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('orders.index')
                ->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
