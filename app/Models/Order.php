<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'payment_status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_PAID     = 'paid';
    const PAYMENT_FAILED   = 'failed';
    const PAYMENT_EXPIRED  = 'expired';

    // Order dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Satu order memiliki banyak tiket
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Label status pembayaran
    public function paymentStatusLabel(): string
    {
        return match($this->payment_status) {
            'pending' => 'Menunggu Pembayaran',
            'paid'    => 'Lunas',
            'failed'  => 'Gagal',
            'expired' => 'Kadaluarsa',
            default   => ucfirst($this->payment_status),
        };
    }

    // Warna badge status pembayaran
    public function paymentStatusColor(): string
    {
        return match($this->payment_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'paid'    => 'bg-green-100 text-green-800',
            'failed'  => 'bg-red-100 text-red-800',
            'expired' => 'bg-gray-100 text-gray-800',
            default   => 'bg-gray-100 text-gray-800',
        };
    }

    // Total harga format Rupiah
    public function formattedTotalPrice(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}
