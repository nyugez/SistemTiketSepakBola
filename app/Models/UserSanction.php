<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sanction_type',
        'reason',
        'status',
        'banned_by',
        'revoked_by',
        'revoked_at',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * User/suporter yang dikenai sanksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin yang memberikan sanksi
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Admin yang mencabut sanksi
     */
    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Cek apakah sanksi masih aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Label jenis sanksi dalam Bahasa Indonesia
     */
    public function sanctionTypeLabel(): string
    {
        return match ($this->sanction_type) {
            'blacklist' => 'Blacklist Permanen / Dilarang Masuk Stadion',
            'suspend'   => 'Skorsing Sementara',
            'warning'   => 'Peringatan Keras',
            default     => ucfirst($this->sanction_type),
        };
    }

    /**
     * Warna badge jenis sanksi (Tailwind CSS)
     */
    public function sanctionTypeBadgeColor(): string
    {
        return match ($this->sanction_type) {
            'blacklist' => 'bg-red-100 text-red-800 border-red-200',
            'suspend'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'warning'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            default     => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
