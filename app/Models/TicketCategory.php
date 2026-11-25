<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $fillable = [
        'football_match_id',
        'name',
        'price',
        'quota',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quota' => 'integer',
    ];

    // Kategori tiket milik satu pertandingan
    public function footballMatch()
    {
        return $this->belongsTo(FootballMatch::class);
    }

    // Satu kategori tiket memiliki banyak tiket yang dijual
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Sisa kuota tiket
    public function remainingQuota(): int
    {
        return max(0, $this->quota - $this->tickets()
            ->whereHas('order', function ($query) {
                $query->whereIn('payment_status', ['pending', 'paid']);
            })
            ->count());
    }

    // Harga dalam format Rupiah
    public function formattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
