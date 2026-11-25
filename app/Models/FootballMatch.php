<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FootballMatch extends Model
{
    protected $fillable = [
        'home_team',
        'away_team',
        'stadium_id',
        'match_date',
        'status',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    // Status yang tersedia
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ONGOING   = 'ongoing';
    const STATUS_FINISHED  = 'finished';
    const STATUS_CANCELLED = 'cancelled';

    // Pertandingan diadakan di satu stadion
    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

    // Pertandingan memiliki banyak kategori tiket
    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    // Label status dalam bahasa Indonesia
    public function statusLabel(): string
    {
        return match($this->status) {
            'scheduled' => 'Dijadwalkan',
            'ongoing'   => 'Sedang Berlangsung',
            'finished'  => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->status),
        };
    }

    // Warna badge status (Tailwind class)
    public function statusColor(): string
    {
        return match($this->status) {
            'scheduled' => 'bg-blue-100 text-blue-800',
            'ongoing'   => 'bg-green-100 text-green-800',
            'finished'  => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default     => 'bg-gray-100 text-gray-800',
        };
    }
}
