<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Menampilkan halaman rincian & konfirmasi pembayaran
     */
    public function show(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if (Auth::user()->isBanned()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Akses Ditolak: Akun Anda sedang terkena blacklist/sanksi suporter dan tidak dapat memproses pembayaran.');
        }

        $order->load(['tickets.ticketCategory.footballMatch.stadium']);
        return view('payment.show', compact('order'));
    }

    /**
     * Simulasi konfirmasi pembayaran secara langsung oleh pengguna
     */
    public function confirm(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        if (Auth::user()->isBanned()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Akses Ditolak: Akun Anda dalam status sanksi/blacklist dan tidak dapat mengonfirmasi pembayaran tiket.');
        }

        abort_if(
            $order->payment_status !== Order::PAYMENT_PENDING && $order->payment_status !== 'pending',
            422,
            'Order sudah diproses atau tidak dalam status pending.'
        );

        DB::beginTransaction();
        try {
            // Update status pembayaran menjadi 'paid'
            $order->update(['payment_status' => Order::PAYMENT_PAID]);

            // Generate QR Code unik untuk setiap tiket yang belum memiliki kode QR
            foreach ($order->tickets as $ticket) {
                if (empty($ticket->qr_code)) {
                    $ticket->update([
                        'qr_code' => Ticket::generateQrCode($order->id, $ticket->id),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Pembayaran berhasil dikonfirmasi! E-Ticket Anda sudah aktif.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Menangani respons/notification webhook dari Payment Gateway (misal: Midtrans / Xendit)
     *
     * Memvalidasi status transaksi dan memperbarui status pada tabel 'orders' menjadi 'paid' atau 'failed'.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request)
    {
        $payload = $request->all();
        Log::info('Payment Gateway Webhook Callback Received:', $payload);

        // Ekstraksi Order ID dari berbagai format payment gateway:
        // Midtrans: order_id; Xendit: external_id; Generic: id / order_id
        $rawOrderId = $request->input('order_id')
            ?? $request->input('external_id')
            ?? $request->input('id');

        if (!$rawOrderId) {
            Log::warning('Webhook callback error: order_id tidak ditemukan pada payload.');
            return response()->json([
                'status'  => 'error',
                'message' => 'Parameter order_id atau external_id tidak ditemukan.',
            ], 400);
        }

        // Normalisasi order ID jika menggunakan format string ber-prefix, misal "ORDER-123" -> 123
        $orderId = preg_replace('/[^0-9]/', '', (string) $rawOrderId);

        $order = Order::with('tickets')->find($orderId);

        if (!$order) {
            Log::warning("Webhook callback error: Order ID #{$orderId} tidak ditemukan di database.");
            return response()->json([
                'status'  => 'error',
                'message' => "Order #{$orderId} tidak ditemukan.",
            ], 404);
        }

        // Ambil status transaksi dari webhook
        // Midtrans: transaction_status; Xendit: status
        $transactionStatus = strtolower(
            (string) ($request->input('transaction_status') ?? $request->input('status') ?? '')
        );
        $fraudStatus = strtolower((string) $request->input('fraud_status', 'accept'));

        // Evaluasi status pembayaran
        $isPaid = false;
        $isFailed = false;

        // 1. Sukses / Lunas
        if (
            in_array($transactionStatus, ['settlement', 'paid', 'completed']) ||
            ($transactionStatus === 'capture' && $fraudStatus === 'accept')
        ) {
            $isPaid = true;
        }
        // 2. Gagal / Dibatalkan / Kadaluarsa
        elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'expired', 'failed'])) {
            $isFailed = true;
        }

        DB::beginTransaction();
        try {
            if ($isPaid) {
                // Perbarui status pesanan menjadi 'paid'
                $order->update(['payment_status' => Order::PAYMENT_PAID]);

                // Generate QR Code unik untuk setiap tiket
                foreach ($order->tickets as $ticket) {
                    if (empty($ticket->qr_code)) {
                        $ticket->update([
                            'qr_code' => Ticket::generateQrCode($order->id, $ticket->id),
                        ]);
                    }
                }

                Log::info("Order #{$order->id} berhasil diperbarui menjadi PAID via webhook.");
            } elseif ($isFailed) {
                // Perbarui status pesanan menjadi 'failed'
                $order->update(['payment_status' => Order::PAYMENT_FAILED]);

                Log::info("Order #{$order->id} diperbarui menjadi FAILED via webhook.");
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status pesanan berhasil diperbarui.',
                'data'    => [
                    'order_id'       => $order->id,
                    'payment_status' => $order->payment_status,
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error memproses webhook untuk Order #{$order->id}: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat memperbarui status pesanan.',
            ], 500);
        }
    }
}
