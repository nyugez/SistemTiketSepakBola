<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'order_id',
        'ticket_category_id',
        'seat_number',
        'qr_code',
    ];

    // Tiket milik satu order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Tiket termasuk kategori tiket tertentu
    public function ticketCategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    // Shortcut ke pertandingan melalui kategori tiket
    public function footballMatch()
    {
        return $this->ticketCategory->footballMatch;
    }

    // Generate kode unik untuk QR Code
    public static function generateQrCode(int $orderId, int $ticketId): string
    {
        return 'TKT-' . strtoupper(substr(md5($orderId . '-' . $ticketId . '-' . now()), 0, 12));
    }
}
