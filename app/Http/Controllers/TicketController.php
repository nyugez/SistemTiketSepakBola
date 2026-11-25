<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar E-Ticket aktif milik pengguna yang sedang login setelah pembayaran sukses
     *
     * @return \Illuminate\View\View
     */
    public function myTickets()
    {
        $tickets = Ticket::with(['ticketCategory.footballMatch.stadium', 'order.user'])
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id())
                      ->where('payment_status', Order::PAYMENT_PAID);
            })
            ->latest()
            ->paginate(9);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Menampilkan detail satu E-Ticket (lengkap dengan QR Code)
     *
     * @param int|string $id ID tiket
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $ticket = Ticket::with(['order.user', 'ticketCategory.footballMatch.stadium'])
            ->findOrFail($id);

        // Hanya pemilik pesanan yang boleh melihat tiket
        abort_if($ticket->order->user_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if(
            $ticket->order->payment_status !== Order::PAYMENT_PAID && $ticket->order->payment_status !== 'paid',
            403,
            'Tiket belum dibayar atau tidak aktif.'
        );

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Menampilkan/mengunduh E-Ticket (PDF lengkap dengan kode unik tiket)
     *
     * @param int|string $id ID tiket
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $ticket = Ticket::with(['order.user', 'ticketCategory.footballMatch.stadium'])
            ->findOrFail($id);

        // Pastikan hanya pemilik tiket yang dapat mengunduh dan status pesanan adalah 'paid'
        abort_if($ticket->order->user_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if(
            $ticket->order->payment_status !== Order::PAYMENT_PAID && $ticket->order->payment_status !== 'paid',
            403,
            'Tiket belum lunas.'
        );

        // Pastikan tiket memiliki kode unik QR
        if (empty($ticket->qr_code)) {
            $ticket->update([
                'qr_code' => Ticket::generateQrCode($ticket->order_id, $ticket->id),
            ]);
        }

        // Render template PDF untuk E-Ticket
        $pdf = Pdf::loadView('tickets.pdf', compact('ticket'))
            ->setPaper('a5', 'portrait');

        $fileName = 'e-ticket-' . ($ticket->qr_code ?: $ticket->id) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Alias method untuk download PDF via Route Model Binding (kompatibilitas)
     *
     * @param Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Ticket $ticket)
    {
        return $this->download($ticket->id);
    }
}
