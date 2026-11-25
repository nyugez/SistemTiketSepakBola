<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
        'ban_reason',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_banned'         => 'boolean',
            'banned_at'         => 'datetime',
        ];
    }

    // Cek apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah user sedang terkena sanksi/blacklist
    public function isBanned(): bool
    {
        return (bool) $this->is_banned;
    }

    // Relasi: user memiliki banyak order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relasi: riwayat semua sanksi yang diterima suporter
    public function sanctions()
    {
        return $this->hasMany(UserSanction::class, 'user_id')->latest();
    }

    // Relasi: sanksi aktif saat ini (jika ada)
    public function activeSanctions()
    {
        return $this->hasMany(UserSanction::class, 'user_id')->where('status', 'active')->latest();
    }

    // Relasi: sanksi aktif terkini
    public function latestActiveSanction()
    {
        return $this->hasOne(UserSanction::class, 'user_id')->where('status', 'active')->latestOfMany();
    }
}
